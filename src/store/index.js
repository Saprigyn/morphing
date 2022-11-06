import {defineStore} from 'pinia';
import axios from 'axios';
import shuffle from 'lodash/shuffle';
import range from 'lodash/range';
import padStart from 'lodash/padStart';
import {logText} from "../utils/logger.js";

const userMax = 20000;
const userMin = 10000;
const labelCodes = [0, 64, 32, 96, 16, 80, 48, 112, 8, 72, 40, 104, 24, 88, 56, 120, 4];
const comHeader = 109;
const comOpenCommandStart = 112;
const comOpenCommandEnd = 50;
const comLabelCommand = 104;

const imgVariations = ['1n', '2n', '1s', '1h', '0x', '0y'];

const unit = Math.floor(Math.random() * 1000) + 1;
const maxImage = Math.floor(150);

let port, writer, curImage, order, weights, weich;

const resetValues = () => {
    curImage = 0;
    order = range(imgVariations.length);
    weights = range(5, imgVariations.length + 5, 0);
    weich = range(2, imgVariations.length + 2, 0);
};

resetValues();

export const useStore = defineStore('main', {
    state: () => ({
        user: Math.floor(Math.random() * (userMax - userMin) + userMin),
        name: 'Друг',
        comConnected: false,
        debugMode: false,
        comAvailable: 'serial' in navigator,
        working: false,
        imgSrc: null,
        imgName: null,
        frame: null,
        end: false,
        curIndex: 0,
    }),
    actions: {
        recalculateWeights(modifyWeights = 0) {
            if (modifyWeights > 0) {
                weights[this.curIndex] = weights[this.curIndex] + weich[this.curIndex];

                if (weich[this.curIndex] > 1) {
                    weich[this.curIndex] = weich[this.curIndex] - 1;
                }

                if (weights[this.curIndex] > 10) {
                    weights[this.curIndex] = 6;
                    weich[this.curIndex] = 2;
                }
            } else if (modifyWeights < 0) {
                weights[this.curIndex] = weights[this.curIndex] - weich[this.curIndex];

                if (weich[this.curIndex] > 1) {
                    weich[this.curIndex] = weich[this.curIndex] - 1;
                }

                if (weights[this.curIndex] < 1) {
                    weights[this.curIndex] = 4;
                    weich[this.curIndex] = 2;
                }
            }
        },
        nextImage() {
            curImage++;
            logText(`Image ${curImage} of ${maxImage}`).then();

            if (curImage > maxImage) {
                resetValues();
                this.end = true;
            } else {
                this.end = false;
                if (order.length <= 1) {
                    this.sendToCom(2).then();

                    let newOrder;

                    do {
                        newOrder = shuffle(range(imgVariations.length));
                    } while (order[0] === newOrder[0]);

                    order = newOrder;

                    logText(`New image order will be: ${order}`).then();
                }

                this.curIndex = order.shift();

                this.frame = padStart(this.curIndex === 4 ? Math.floor(Math.random() * 10) + 1 : weights[this.curIndex], 3, '0');

                this.$patch({
                    imgSrc: `//faces.rudych.ru/outs/${this.user}/e${imgVariations[this.curIndex]}/frame${this.frame}.png`,
                    imgName: `${imgVariations[this.curIndex]}0${this.frame}`,
                });

                logText(`New image: ${this.imgSrc}`).then();
            }
        },
        async sendToServer(buttonNumber, latency) {
            logText(`Sending to server frame ${this.frame}`).then();

            await axios.post(
                '//morph.rudych.ru/protokey.php',
                {
                    param: window.JSON.stringify({
                        unit,
                        v: this.frame,
                        img: this.imgName,
                        bnt: buttonNumber,
                        key: this.user,
                        latency,
                    })
                },
                {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }
            );
        },
        async sendToCom(labelNumber) {
            if (!port) {
                return;
            }

            try {
                await writer.write(
                    new Uint8Array(
                        [comHeader, comLabelCommand, labelCodes[labelNumber], 0]
                    )
                );
            } catch (e) {
                writer = port.writable.getWriter();
                await this.sendToCom(labelNumber);
            }
        },
        async connectCom() {
            const ports = await navigator.serial.getPorts();

            if (!ports.length)
                port = await navigator.serial.requestPort();
            else {
                port = ports[0];
            }

            await port.open({
                baudRate: 115200,
                stopBits: 1,
                parity: 'none',
            });
            writer = port.writable.getWriter();

            await writer.write(
                new Uint8Array(
                    [comHeader, comOpenCommandStart, comOpenCommandEnd, 0, 0, 0]
                )
            );

            this.comConnected = true;
        },
        async disconnectCom() {
            if (port) {
                await writer.releaseLock();
                await port.close();
            }
        },
    },
});
