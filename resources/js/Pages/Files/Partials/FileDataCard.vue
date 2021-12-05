<template>
    <auto-data-card :data="file" :fields="fields" :titleField="titleField" :actions="actions" model-name="file">
        <template #title>
            <div class="flex flex-row nowrap items-center gap-4 truncate">
                <file-extension-icon
                    :extension="file.extension"
                    v-if="
                        !['jpg', 'jpeg', 'png', 'svg', 'webp'].includes(
                            file.extension
                        )
                    "
                    class="h-16 w-16"
                />
                <img
                    :src="file.url"
                    class="h-16 w-16 object-cover rounded-sm"
                    v-else
                />
                <a
                    :href="file.url"
                    :download="file.filename"
                    class="link link-hover truncate"
                    :title="file.filename"
                >
                    {{ file.filename }}
                </a>
            </div>
        </template>
    </auto-data-card>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import AutoDataCard from "@/Components/CRUD/AutoDataCard";
import FileExtensionIcon from "./FileExtensionIcon";

export default {
    components: {
        FontAwesomeIcon,
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
        actions:{
            type: Object,
            default: {}
        }
    },
};
</script>
