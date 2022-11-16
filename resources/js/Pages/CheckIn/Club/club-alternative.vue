<template>
    <div class="alt-desc">Or enter...</div>
    <club-alternative-member v-if="tab === 'member'" />
    <div class="alt-guest" v-if="tab === 'guest'">
        <div class="alt-pass-num">Free Pass Number</div>
    </div>
    <div class="alt-employee" v-if="tab === 'employee'">
        <div class="alt-employee-num">Employee Number</div>
    </div>
</template>
<style scoped>
.alt-desc {
    @apply text-center pb-3 mt-8;
}

.alt-guest,
.alt-employee {
    @apply flex mb-11 justify-center;
}
.alt-pass-num {
    @apply w-fit font-semibold bg-success/60 py-2 px-7 rounded;
}
.alt-employee-num {
    @apply w-fit font-semibold bg-base-content/10 py-2 px-7 rounded;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import ClubAlternativeMember from "./club-alternative-member.vue";
const props = defineProps({
    tab: String,
});
</script>
