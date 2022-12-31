<template>
    <daisy-modal
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <div
            class="border border-secondary bg-black p-4 rounded-md max-w-sm w-full"
        >
            <div class="flex my-2">
                <h1 class="text-secondary text-2xl mr-4">Sources</h1>
                <button
                    @click="() => (entityTab = 'leads')"
                    class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md"
                    :class="{
                        'bg-secondary': entityTab === 'leads',
                    }"
                >
                    Leads
                </button>
                <button
                    @click="() => (entityTab = 'members')"
                    class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md ml-2"
                    :class="{
                        'bg-secondary': entityTab === 'members',
                    }"
                >
                    Members
                </button>
            </div>

            <ul class="px-4 mt-4">
                <li
                    v-if="entityTab === 'leads'"
                    v-for="(srcObj, ix) in leadTypes"
                    class="flex items-center"
                >
                    <input
                        type="checkbox"
                        :id="srcObj.name"
                        :checked="leadTypes[ix].selected"
                        v-model="leadTypes[ix].selected"
                    />
                    <label
                        :for="srcObj.name"
                        class="ml-4 text-lg my-1 capitalize"
                        >{{ srcObj.name }}</label
                    >
                </li>
                <li
                    v-if="entityTab === 'members'"
                    v-for="(srcObj, ix) in membershipTypes"
                    class="flex items-center"
                >
                    <input
                        type="checkbox"
                        :id="srcObj?.id"
                        :checked="membershipTypes[ix].selected"
                        v-model="membershipTypes[ix].selected"
                    />
                    <label
                        :for="srcObj?.id"
                        class="ml-4 text-lg my-1 capitalize"
                        >{{ srcObj.name }}</label
                    >
                </li>
            </ul>

            <div class="flex flex-col py-2 mt-2">
                <label class="text-2xl mb-4 text-secondary" for="audience-name">
                    Audience Title
                </label>
                <input v-model="titleVal" id="audience-name" type="text" />
            </div>
        </div>

        <div class="flex justify-between max-w-sm w-full mt-6">
            <button
                @click="
                    $emit('canceled');
                    clearSelected();
                "
                :disabled="loading"
            >
                Back
            </button>
            <button
                @click="handleSaveCheck"
                class="px-2 py-1 border-secondry border rounded-md hover:bg-secondary transition-all disabled:opacity-20 disabled:cursor-not-allowed"
            >
                Save
            </button>
            <span class="w-10"></span>
        </div>
    </daisy-modal>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

import axios from "axios";

import DaisyModal from "@/Components/DaisyModal.vue";
import { toastInfo, toastError } from "@/utils/createToast";

const emit = defineEmits(["save", "canceled"]);

const props = defineProps({
    audience: {
        type: Object,
        default: {},
    },
    membershipTypes: {
        type: Array,
        required: true,
    },
    leadTypes: {
        type: Array,
        required: true,
    },
    endpoint: {
        type: String,
        default: "mass-comms.audiences.update",
    },
});

/** Component State */
const entityTab = ref(
    props?.audience?.entity === "App\\Domain\\Users\\Models\\Member"
        ? "members"
        : "leads"
);
const titleVal = ref(props?.audience?.title ?? "");
const loading = ref(false);
const leadTypes = ref(props.leadTypes);
const membershipTypes = ref(props.membershipTypes);
const allSources = computed(() => {
    return [...leadTypes.value, ...membershipTypes.value];
});
const selectedSources = computed(() =>
    allSources.value.filter((s) => s.selected)
);
const selectedIds = computed(() => selectedSources.value.map((s) => s.id));

/**
 * check for invalid data and return an informative message
 * to the user to fix it before saving
 */
const saveDisabled = computed(() => {
    if (titleVal.value === "New Audience" || titleVal.value.trim() === "")
        return "You must name your audience.";
    if (selectedIds.value.length === 0)
        return "You must select at least one filter.";
    return false;
});

const clearSelected = () => {
    return allSources.value.forEach((src) => {
        src.selected = false;
        return src;
    });
};

const handleSaveCheck = () => {
    if (typeof saveDisabled.value !== "string") {
        handleSave();
        return;
    }
    toastError(saveDisabled.value);
};

/**
 * Attempt to save the modifications on the back end
 */
const handleSave = async () => {
    if (typeof saveDisabled === "string") {
        toastError(saveDisabled);
        return;
    }

    try {
        let filters = { lead_type_id: selectedIds.value };
        let entity = "App\\Domain\\EndUsers\\Leads\\Projections\\Lead";
        if (entityTab.value === "members") {
            filters = { membership_type_id: selectedIds.value };
            entity = "App\\Domain\\Users\\Models\\Member";
        }
        const moddedAudience = {
            id: props.audience.id,
            name: titleVal.value,
            entity,
            filters,
        };

        loading.value = true;
        let newData;
        if (props.endpoint.includes("update")) {
            newData = await axios.put(
                route(props.endpoint, moddedAudience.id),
                {
                    ...moddedAudience,
                }
            );
        } else if (props.endpoint.includes("create")) {
            newData = await axios.post(route(props.endpoint), {
                ...moddedAudience,
            });
        } else {
            throw new Error("unknown endpoint", props.endpoint);
        }

        loading.value = false;

        if (newData.status === 200) {
            toastInfo("Audience '" + titleVal?.value + "' updated!");
            emit("save", newData.data);
            clearSelected();
        }
    } catch (err) {
        loading.value = false;
        toastError("Problem Saving Audience");
        console.log("problem saving audience", err);
    }
};

onMounted(() => {
    let filters = props.audience?.filters;
    if (!filters) return;

    membershipTypes.value = membershipTypes.value.map((s) => {
        return filters?.membership_type_id?.includes(s.id)
            ? { ...s, selected: true }
            : s;
    });

    leadTypes.value = leadTypes.value.map((s) => {
        return filters?.lead_type_id?.includes(s.id)
            ? { ...s, selected: true }
            : s;
    });
});
</script>
