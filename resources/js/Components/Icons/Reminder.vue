<template>
    <font-awesome-icon icon="bell-on" v-bind="$attrs" size="2x" />
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faBellOn } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
library.add(faBellOn);
</script>
