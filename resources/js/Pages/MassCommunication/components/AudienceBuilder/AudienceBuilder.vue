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
                    @click="() => (currentTab = 'leads')"
                    class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md"
                    :class="{
                        'bg-secondary': currentTab === 'leads',
                    }"
                >
                    Leads
                </button>
                <button
                    @click="() => (currentTab = 'members')"
                    class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md ml-2"
                    :class="{
                        'bg-secondary': currentTab === 'members',
                    }"
                >
                    Members
                </button>
            </div>

            <ul class="px-4 mt-4">
                <li
                    v-if="currentTab === 'leads'"
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
                    v-if="currentTab === 'members'"
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
                <input v-model="form.title" id="audience-name" type="text" />
            </div>
        </div>
        <div class="flex justify-between max-w-sm w-full mt-6">
            <button @click="$emit('close')" :disabled="loading">Back</button>
            <button
                @click="handleSave"
                :disabled="form.name === '' || loading"
                class="px-2 py-1 border-secondry border rounded-md hover:bg-secondary transition-all disabled:opacity-20 disabled:cursor-not-allowed"
            >
                Save
            </button>
            <span class="w-10"></span>
        </div>
    </daisy-modal>
</template>

<script setup>
import * as _ from "lodash";
import mutations from "@/gql/mutations";
import { useMutation } from "@vue/apollo-composable";
import { ref, computed, onMounted } from "vue";
import { useGymRevForm } from "@/utils";
import DaisyModal from "@/Components/DaisyModal.vue";
import { toastInfo, toastError } from "@/utils/createToast";

const props = defineProps({
    audience: {
        type: Object,
        default: {
            id: "",
            name: "",
            filters: {
                membership_type_id: [],
                lead_type_id: [],
            },
        },
    },
    membershipTypes: {
        type: Array,
        required: true,
    },
    leadTypes: {
        type: Array,
        required: true,
    },
});

const currentTab = ref("leads");

const { mutate: createAudience } = useMutation(mutations.audience.create);
const { mutate: updateAudience } = useMutation(mutations.audience.update);

const form = useGymRevForm({
    ...props.audience,
});

const operFn = computed(() => {
    return props.audience.id === "" ? createAudience : updateAudience;
});

const handleSave = async () => {
    loading.value = true;
    await operFn.value({
        id: form?.id,
        name: form.title,
        filters: {
            lead_type_id: currentTab.value === "leads" ? idsLead.value : [],
            membership_type_id:
                currentTab.value === "leads" ? [] : idsMember.value,
        },
        entity: "App\\Domain\\EndUsers\\Leads\\Projections\\Lead",
    });
    loading.value = false;
};

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
// const saveDisabled = computed(() => {
//     if (titleVal.value === "New Audience" || titleVal.value.trim() === "")
//         return "You must name your audience.";
//     if (selectedIds.value.length === 0)
//         return "You must select at least one filter.";
//     return false;
// });
// const clearSelected = () => {
//     return allSources.value.forEach((src) => {
//         src.selected = false;
//         return src;
//     });
// };

const idsLead = computed(() => {
    let ids = [];
    leadTypes.value.forEach((obj) => {
        if (obj.selected) {
            ids.push(obj.id);
        }
    });
    return ids;
});

const idsMember = computed(() => {
    let ids = [];
    membershipTypes.value.forEach((obj) => {
        if (obj.selected) {
            ids.push(obj.id);
        }
    });
    return ids;
});

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
