<template>
    <font-awesome-icon icon="notes" v-bind="$attrs" size="2x" />
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faNotesMedical } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
library.add(faNotesMedical);
</script>
