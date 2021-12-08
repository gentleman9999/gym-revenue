<template>
    <auto-data-card v-bind="$props">
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
import AutoDataCard from "@/Components/CRUD/AutoDataCard";
import FileExtensionIcon from "./FileExtensionIcon";

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
        actions:{
            type: Object,
            default: {}
        }
    },
};
</script>
