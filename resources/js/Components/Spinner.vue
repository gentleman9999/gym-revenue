<template>
    <font-awesome-icon
        icon="spinner"
        size="6x"
        class="self-center opacity-10 fa-spin justify-self-center"
    />
</template>
<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faSpinner } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faSpinner);
</script>
