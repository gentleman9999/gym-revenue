<template>
    <li :id="template?.id" class="m-2 mx-4 max-w-xs min-h-[16rem]">
        <div
            :class="{ 'opacity-25 hover:opacity-100': trash_template }"
            class="bg-black p-2 relative bg-opacity-25 border-secondary rounded-md transition-all flex flex-col border items-center"
        >
            <p
                class="max-w-[15rem] overflow-hidden whitespace-nowrap overflow-ellipsis text-secondary text-lg mx-4 font-bold capitalize"
            >
                {{ template?.name }}
            </p>
            <img
                :src="template?.thumbnail?.url"
                alt="template preview"
                class="aspect-[4/3] max-w-[10rem] my-4 object-contain object-center"
            />

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

import DateDetails from "../DateDetails.vue";
import LoadSpin from "../svg/LoadSpin.vue";
import TemplateAction from "../TemplateAction.vue";

import PenSquare from "../svg/PenSquare.vue";
import TrashHollow from "../svg/TrashHollow.vue";
import TrashUndo from "../svg/TrashUndo.vue";

const emit = defineEmits(["edit", "trash", "restore"]);

const awaitingTrash = ref(false);

const thisTemplateBusy = computed(() => {
    if (props.template?.id === awaitingTrash.value) return true;
    return false;
});

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

    emit(action, "email", props.template);
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
</style>
