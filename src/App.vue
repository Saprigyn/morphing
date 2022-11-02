<script setup>
import {useStore} from './store';
import ComConnector from "./components/ComConnector.vue";

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
</script>

<template>
<header>
    <a>Аз есмь: {{ store.user }}</a>
    <a :href="`//faces.rudych.ru/route.php?key=${store.user}&name=${store.name}&route=111`">Пойду дальше на
        опросники</a>
</header>
<h1 v-if="!store.comAvailable && !store.debugMode">Данный браузер не поддерживается!</h1>
<ComConnector v-else-if="!store.debugMode"/>
</template>

<style scoped>
header a {
    padding: 10px
}
</style>
