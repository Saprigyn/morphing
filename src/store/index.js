import {defineStore} from 'pinia';

const userMax = 20000;
const userMin = 10000;
const labelCodes = [0, 64, 32, 96, 16, 80, 48, 112, 8, 72, 40, 104, 24, 88, 56, 120, 4];
const comHeader = 109;
const comOpenCommandStart = 112;
const comOpenCommandEnd = 50;
const comLabelCommand = 104;

let port, writer;

export const useStore = defineStore('main', {
    state: () => ({
        user: Math.floor(Math.random() * (userMax - userMin) + userMin),
        name: 'Друг',
        comConnected: false,
        debugMode: false,
        comAvailable: 'serial' in navigator,
    }),
    actions: {
        async connectCom() {
            port = await navigator.serial.requestPort();
            await port.open({
                baudRate: 115200,
                stopBits: 1,
                parity: "none"
            });
            writer = port.writable.getWriter();

            await writer.write(
                new Uint8Array([comHeader, comOpenCommandStart, comOpenCommandEnd, 0, 0, 0]));

            await writer.close()

            this.comConnected = true;
        },
    },
});
