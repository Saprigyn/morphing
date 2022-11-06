<script setup>
import {useStore} from "../store/index.js";

const store = useStore();
</script>

<template>
<o-button @click="store.connectCom()">Подключить СOM</o-button>
</template>
