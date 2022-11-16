<template>
    <div class="update-member-container">
        <div class="update-member-title">Add or Change Members</div>
        <div class="update-member-btn">
            <font-awesome-icon
                :icon="['fas', 'plus-circle']"
                size="lg"
                class="mx-1 cursor-pointer"
                @click="$emit('toggle-mode')"
            />
        </div>
    </div>
</template>
<style scoped>
.update-member-container {
    @apply mb-6;
}
.update-member-title {
    @apply text-secondary font-semibold text-sm mt-6;
}
.update-member-btn {
    @apply flex text-secondary justify-center items-center mt-1;
    &:before,
    &:after {
        content: "";
        @apply w-full border-b-2;
    }
}
</style>
<script setup>
import AddIcon from "@/Components/Icons/Add.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faPlusCircle } from "@fortawesome/pro-solid-svg-icons";
import { computed } from "vue";
library.add(faPlusCircle);
</script>
