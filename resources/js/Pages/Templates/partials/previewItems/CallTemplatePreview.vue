<template>
    <li :id="template?.id" class="m-4 max-w-xs min-h-[12rem]">
        <div
            :class="{ 'opacity-25 hover:opacity-100': trash_template }"
            class="bg-black relative min-w-[15rem] p-2 bg-opacity-25 border-secondary rounded-md transition-all flex flex-col border items-center"
        >
            <p
                class="max-w-[15rem] overflow-hidden whitespace-nowrap overflow-ellipsis text-secondary text-lg mx-4 font-bold capitalize"
            >
                {{ template?.name }}
            </p>
            <div class="w-full text-xs relative">
                <p
                    class="w-56 h-36 leading-4 bg-base-content stripebg bg-opacity-80 font-mono font-bold tracking-wider text-black p-4 py-8 my-8 mx-6 rounded-sm overflow-hidden overflow-ellipsis"
                >
                    {{ truncateEllipsis(template?.script) }}
                </p>

                <ScrollSvg
                    class="absolute top-9 left-8 opacity-25"
                    :height="18"
                    :width="18"
                />
            </div>

            <DateDetails
                :template="template"
                :trash_template="trash_template"
            />

            <div
                class="absolute top-0 left-0 right-0 bottom-0 bg-black bg-opacity-80 stroke-base-content fill-base-content"
                v-if="thisTemplateBusy"
            >
                <LoadSpin :height="'100%'" :width="'100%'" />
            </div>

            <!-- restore trash templates -->
            <button
                v-if="trash_template && permissions.restore"
                @click="() => handleTemplateAction('restore')"
                type="button"
                class="totalcenter transition-all opacity-0 hover:opacity-100 bg-opacity-25 bg-secondary -mt-2 h-full w-full absolute fill-white"
            >
                <div
                    class="bg-black bg-opacity-25 rounded-full p-4 totalcenter"
                >
                    <TrashUndo />
                    <p>Restore</p>
                </div>
            </button>

            <!-- current template actions -->
            <div v-if="!trash_template" class="flex px-2">
                <TemplateAction
                    class="fill-secondary"
                    v-if="permissions.update"
                    @click="() => handleTemplateAction('edit')"
                    :disabled="thisTemplateBusy"
                >
                    <template #icon>
                        <PenSquare width="24" height="24" />
                    </template>

                    <template #text>Edit</template>
                </TemplateAction>

                <TemplateAction
                    class="fill-red-700"
                    v-if="permissions.trash"
                    @click="() => handleTemplateAction('trash')"
                    :disabled="thisTemplateBusy"
                >
                    <template #icon>
                        <TrashHollow width="24" height="24" />
                    </template>

                    <template #text>Trash</template>
                </TemplateAction>
            </div>
        </div>
    </li>
</template>

<script setup>
import { ref, computed } from "vue";
import { truncateEllipsis } from "../../components/helpers";

import DateDetails from "../DateDetails.vue";
import ScrollSvg from "../svg/ScrollSvg.vue";
import LoadSpin from "../svg/LoadSpin.vue";
import TemplateAction from "../TemplateAction.vue";

import PenSquare from "../svg/PenSquare.vue";
import TrashHollow from "../svg/TrashHollow.vue";
import TrashUndo from "../svg/TrashUndo.vue";

const awaitingTrash = ref(null);

const thisTemplateBusy = computed(() => {
    if (props.template?.id === awaitingTrash.value) return true;
    return false;
});

const emit = defineEmits(["edit", "trash", "restore"]);

const props = defineProps({
    template: {
        type: Object,
        default: {},
    },
    trash_template: {
        type: Boolean,
        default: false,
    },
    permissions: {
        type: Object,
        default: {},
    },
});

const handleTemplateAction = (action = "edit") => {
    if (action === "trash") {
        awaitingTrash.value = props.template?.id;
    }

    emit(action, "call", props.template);
};
</script>

<style scoped>
.scaletime {
    @apply duration-300 ease-in-out transition-all;
}

button:active {
    @apply opacity-50 transition-opacity ease-out;
}

button:hover {
    @apply shadow-lg;
}

.stripebg {
    @apply leading-[1.5rem];
    background-image: linear-gradient(
        0deg,
        #d6d5ce 4.55%,
        #f0f0f0 4.55%,
        #f0f0f0 50%,
        #d6d5ce 50%,
        #d6d5ce 54.55%,
        #f0f0f0 54.55%,
        #f0f0f0 100%
    );
    background-size: 3rem 3.2rem;
}
</style>
