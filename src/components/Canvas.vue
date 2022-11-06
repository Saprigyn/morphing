<script setup>
import {useStore} from "../store/index.js";
import {onMounted, ref} from 'vue';
import {logText} from "../utils/logger.js";

const store = useStore();

const buttons = [
    {
        title: 'Я',
        number: 1,
        comLabel: 5,
        modifyWeights: 1,
        size: 'medium',
        class: 'w-48',
        outlined: false,
    },
    {
        title: store.name,
        number: 2,
        comLabel: 6,
        modifyWeights: -1,
        size: 'medium',
        class: 'w-48',
        outlined: false,
    },
    {
        title: 'Незнакомец',
        number: 3,
        comLabel: 7,
        modifyWeights: -1,
        size: 'medium',
        class: 'w-48',
        outlined: false,
    },
    {
        title: 'Пропустить',
        number: 5,
        comLabel: 4,
        modifyWeights: 0,
        size: 'small',
        class: 'w-24',
        outlined: true,
    },
];

const canvas = ref(null);

let ctx, loadTime;

const resetCanvas = () => {
    ctx.fillRect(0, 0, canvas.value.width, canvas.value.height);

    store.sendToCom(1);
};

const crossCanvas = async () => {
    ctx.fillRect(0, 0, canvas.value.width, canvas.value.height);

    const
        x = canvas.value.width / 2,
        y = canvas.value.height / 2,
        xDiff = canvas.value.width / 6,
        yDiff = canvas.value.height / 8;

    ctx.moveTo(x - xDiff, y - yDiff);
    ctx.lineTo(x + xDiff, y + yDiff);

    ctx.moveTo(x + xDiff, y - yDiff);
    ctx.lineTo(x - xDiff, y + yDiff);

    ctx.stroke();

    await store.sendToCom(1);
};

const successCanvas = async () => {
    ctx.fillRect(0, 0, canvas.value.width, canvas.value.height);

    ctx.fillStyle = '#FFFF00';
    ctx.font = 'bold 16px Arial';

    ctx.fillText('Вы успешно выполнили задание', (canvas.value.width / 4) - 17, (canvas.value.height / 4) + 8);
    ctx.fillText('Нажмите вверху страницы ссылку', (canvas.value.width / 4) - 22, (canvas.value.height / 4) + 50);
    ctx.fillText('"перейти к опросникам"', (canvas.value.width / 4) + 15, (canvas.value.height / 4) + 72);

    await store.sendToCom(1);
};

store.$subscribe((mutation, state) => {
    if (state.end){
        successCanvas();
        return;
    }

    if(mutation.type !== 'patch object')
        return;

    const img = new Image();
    img.onload = () => {
        store.sendToCom(1);

        ctx.drawImage(img, 0, 0, canvas.value.width, canvas.value.height);

        loadTime = Date.now();

        store.working = false;
    };
    img.onerror = () => {
        logText('Image not found!');
        store.working = false;
    };
    img.src = `${state.imgSrc}?v=${new Date().getTime()}`;
});

const send = (buttonNumber, comLabel, modifyWeights = 0) => {
    store.working = true;

    const timeDiff = (Date.now() - loadTime) / 1000;

    logText(`Latency: ${timeDiff}`);

    store.sendToCom(comLabel);
    store.sendToServer(buttonNumber, timeDiff);

    store.recalculateWeights(modifyWeights);

    crossCanvas();

    const delay = Math.floor(Math.random() * (500));

    logText(`New delay: ${delay}`);

    setTimeout(resetCanvas, delay + 1000);
    setTimeout(store.nextImage, delay + 2000);
};

onMounted(() => {
    ctx = canvas.value.getContext('2d');

    ctx.fillStyle = '#000000';
    ctx.lineWidth = 5;
    ctx.strokeStyle = '#FFFFFF';

    resetCanvas();
    store.nextImage();
});
</script>

<template>
<div class="mb-3 flex justify-center">
    <canvas ref="canvas" width="450" height="600"/>
</div>
<div class="text-center flex justify-center gap-10 items-center pl-20">
    <div v-for="(button, key) in buttons" >
        <o-button :class="button.class" :disabled="store.working" :key="key"
                  @click="send(button.number, button.comLabel, button.modifyWeights)"
                  :size="button.size"
        :outlined="button.outlined">
            {{ button.title }}
        </o-button>
    </div>
</div>
</template>
