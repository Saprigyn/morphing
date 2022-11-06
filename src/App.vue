<script setup>
import {onBeforeUnmount} from 'vue';
import {useStore} from './store';
import Canvas from "./components/Canvas.vue";
import Header from "./components/Header.vue";

const store = useStore();

const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has('key')) {
    store.$patch({
        user: urlParams.get('key'),
    });
}

if (urlParams.has('name') && urlParams.get('name').length) {
    store.$patch({
        name: urlParams.get('name'),
    });
}

window.toggleAlexanMode = () => {
    store.debugMode = !store.debugMode;
};

onBeforeUnmount(() => {
    store.disconnectCom();
});
</script>

<template>
<Header/>
<Canvas/>
</template>
