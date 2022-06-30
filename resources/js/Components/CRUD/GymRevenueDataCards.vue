<template>
    <div class="wrapper">
        <slot name="pre" />
        <component
            v-for="row in resource?.data || []"
            :is="cardComponent"
            v-bind="{ [modelKey]: row }"
            :data="row"
            :fields="fields"
            :titleField="titleField"
            :actions="actions"
            :model-name="modelName"
            :model-key="modelKey"
            :model-name-plural="modelNamePlural"
            :base-route="baseRoute"
            :has-preview-component="!!previewComponent"
        />
        <div v-if="!resource?.data?.length" class="rounded-xl p-4 bg-base-100">
            <div>
                No
                {{ modelNamePlural || "Records" }}
                found.
            </div>
        </div>
    </div>
</template>
<style scoped>
.wrapper {
    @apply shadow gap-4;
    @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 -my-2 py-2;
    @apply align-middle min-w-full sm:px-6 lg:px-8 sm:-mx-6 lg:-mx-8;
    max-width: 100vw;
}
</style>
<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import AutoDataCard from "@/Components/CRUD/AutoDataCard.vue";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        AutoDataCard,
    },
    props: {
        fields: {
            type: Array,
        },
        resource: {
            type: Object,
        },
        baseRoute: {
            type: String,
            // required: true,
        },
        modelName: {
            type: String,
            default: "Record",
        },
        modelKey: {
            type: String,
            required: true,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        cardComponent: {
            type: Object,
            default: AutoDataCard,
        },
        actions: {
            type: [Object, Boolean],
            default: {},
        },
        previewComponent: {
            type: Object,
        },
    },
    setup(props) {
        let __modelNamePlural = props.modelNamePlural || props.modelName + "s";

        return { modelNamePlural: __modelNamePlural };
    },
};
</script>
