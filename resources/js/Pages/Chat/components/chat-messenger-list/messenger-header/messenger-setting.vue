<template>
    <div class="chat-messenger-setting">
        <font-awesome-icon :icon="['fa', 'cog']" />
    </div>
</template>
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faCog } from "@fortawesome/pro-solid-svg-icons";
library.add(faCog);
</script>
