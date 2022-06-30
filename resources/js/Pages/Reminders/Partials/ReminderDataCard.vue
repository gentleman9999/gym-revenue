<template>
    <auto-data-card v-bind="$props">
        <template #title>
            <div class="flex flex-row nowrap items-center gap-4 truncate">
                <input id="description" class="" value="" />
                <input id="remind_time" class="" value="" />
                <input id="triggered_at" class="" value="" />
            </div>
        </template>
    </auto-data-card>
</template>

<script>
import AutoDataCard from "@/Components/CRUD/AutoDataCard";
import FileExtensionIcon from "./ReminderIcon";

export default {
    components: {
        AutoDataCard,
        FileExtensionIcon,
    },
    props: {
        file: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
};
</script>
