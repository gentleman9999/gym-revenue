<template>
    <font-awesome-icon :icon="['fas', 'chevron-right']" size="lg" />
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faChevronRight } from "@fortawesome/pro-solid-svg-icons";
library.add(faChevronRight);
</script>
