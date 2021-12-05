<template>
    <auto-data-card :data="file" :fields="fields" :titleField="titleField" >
        <template #title>
            <a class="link link-hover" :href="file.url" :download="filename">{{ file.filename }}</a>
        </template>
    </auto-data-card>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import AutoDataCard from "@/Components/CRUD/AutoDataCard";

export default {
    components: {
        FontAwesomeIcon,
        AutoDataCard,
    },
    props: {
        file: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
    },
};
</script>
