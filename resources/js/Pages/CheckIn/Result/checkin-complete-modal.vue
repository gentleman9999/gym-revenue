<template>
    <card-with-title accent :title="data?.title" class="bg-base-200">
        <div class="p-5">
            {{ data?.msg }}
        </div>
    </card-with-title>
</template>
<style scoped></style>
<script setup>
import CardWithTitle from "../components/card-with-title.vue";
const props = defineProps({
    data: Object,
    default: {
        title: "Completed Modal",
        msg: "Hope you enjoyed your PT class!",
    },
});
</script>
