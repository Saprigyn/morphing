<script setup>
import {useStore} from "../store/index.js";

const store = useStore();
</script>

<template>
  <h1>Необходимо подключить порт</h1>

  <div>
    <button type="button" @click="store.connectCom()">Открыть порт</button>
  </div>
</template>
