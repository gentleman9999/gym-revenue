<template >
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
                target="_blank"
                class="link link-hover"
                >{{ file.filename }}</a
            >
        </div>
</template>

<script>
import AutoDataCard from "@/Components/CRUD/AutoDataCard";
import FileExtensionIcon from "./FileExtensionIcon";

export default {
    components: {
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
        actions: {
            type: Object,
            default: {},
        },
    },
};
</script>
