<template>
    <font-awesome-icon icon="sticky-note" v-bind="$attrs" size="2x" />
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faStickyNote } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
library.add(faStickyNote);
</script>
