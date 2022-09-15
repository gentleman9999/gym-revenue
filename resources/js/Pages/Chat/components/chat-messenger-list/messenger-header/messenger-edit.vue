<template>
    <div class="chat-messenger-setting">
        <font-awesome-icon :icon="['fa', 'edit']" />
    </div>
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEdit } from "@fortawesome/pro-solid-svg-icons";
library.add(faEdit);
</script>
