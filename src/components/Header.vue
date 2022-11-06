<script setup>
import { useDark } from '@vueuse/core';
import {useStore} from "../store/index.js";
import ComConnector from "./ComConnector.vue";

const store = useStore();
const isDark = useDark();
</script>

<template>
<header class="flex items-center my-2">
    <div class="basis-1/4 flex gap-3 items-center">
        <ComConnector v-if="!store.comConnected && store.comAvailable"/>
        <span>{{ store.user }}</span>
    </div>
    <div class="basis-1/2 text-center">
        <a :href="`//faces.rudych.ru/route.php?key=${store.user}&name=${store.name}&route=111`" class="text-lg underline">
            Пойду дальше на опросники
        </a>
    </div>
    <div class="basis-1/4 text-right"><o-switch variant="info" passive-variant="warning" v-model="isDark"/></div>
</header>
</template>
