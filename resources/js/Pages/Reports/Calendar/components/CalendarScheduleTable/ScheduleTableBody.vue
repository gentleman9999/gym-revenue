<template>
    <tbody>
        <tr
            v-for="item in props.data"
            :key="item.id"
            class="hover:bg-neutral-300 hover:text-base-100"
        >
            <td class="border-l border-secondary">
                <div>{{ item.location }}</div>
            </td>
            <td>
                <div>{{ item.event }}</div>
            </td>
            <td>
                <div><img :src="item.employee" /></div>
            </td>
            <td>
                <div>{{ item.time }}</div>
            </td>
            <td>
                <select-box
                    label="status"
                    :items="available_status"
                    size="xs"
                    class="float-right"
                    color="secondary"
                />
            </td>
            <td class="border-r border-secondary">
                <Button secondary size="xs">View</Button>
            </td>
        </tr>
    </tbody>
</template>
<style scoped>
td {
    @apply border-y border-secondary h-px py-3;
}
td:not(:nth-last-child(2)) > div {
    @apply flex h-full border-r border-secondary justify-center items-center;
}
</style>
<script setup>
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button";
const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});
const available_status = [
    "Done",
    "To Do",
    "Reschedule",
    "Confirmed",
    "Show",
    "No Show",
];
</script>
