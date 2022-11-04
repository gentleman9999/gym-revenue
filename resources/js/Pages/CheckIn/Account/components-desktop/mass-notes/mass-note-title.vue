<template>
    <div class="mass-note-title-container">
        <div class="mass-note-title">Notes</div>
        <div class="flex flex-row space-x-3">
            <search-input :secondary="false" />
            <div class="mass-note-add">
                <font-awesome-icon :icon="['fas', 'plus']" />
            </div>
        </div>
    </div>
</template>
<style scoped>
.mass-note-title-container {
    @apply flex flex-row bg-secondary justify-between h-12 items-center p-5;
}
.mass-note-title {
    @apply text-lg font-semibold;
}
.mass-note-add {
    @apply flex items-center justify-center border-2 rounded-full w-7 h-7;
}
</style>
<script setup>
import SearchInput from "@/Components/SearchInput.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
library.add(faPlus);
</script>
