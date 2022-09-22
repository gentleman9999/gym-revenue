<template>
    <div
        class="bg-black p-8 border-secondary border rounded-md max-w-5xl w-full"
    >
        <div
            class="p-4 bg-secondary-content text-black border-secondary border rounded-md"
        >
            <h2 class="text-xl font-bold">Script for Phone Call</h2>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Eligendi,
            </p>

            <div class="flex flex-col">
                <label for="callscriptname" class="mt-4">Name</label>
                <input
                    id="callscriptname"
                    v-model="scriptName"
                    placeholder="Call Script Name"
                    type="text"
                    class="border-secondary bg-primary-content mt-1"
                />

                <label for="callscriptscript" class="mt-4">Script</label>
                <textarea
                    id="callscriptscript"
                    v-model="scriptMessage"
                    cols="30"
                    rows="10"
                    class="border-secondary bg-primary-content resize-none mt-1"
                ></textarea>
            </div>

            <div class="flex justify-between">
                <button
                    :disabled="loading"
                    @click="$emit('cancel')"
                    class="disabled:cursor-not-allowed disabled:opacity-25"
                >
                    Cancel
                </button>
                <button
                    @click="handleSubmit"
                    class="px-2 py-1 border-secondary border rounded-md mt-4 bg-secondary transition-all text-base-content hover:bg-base-content hover:text-secondary disabled:opacity-25 disabled:cursor-not-allowed"
                    :disabled="loading"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { toastInfo, toastError } from "@/utils/createToast";
import axios from "axios";

const props = defineProps({
    script: {
        type: String,
        default: "",
    },
    name: {
        type: String,
        default: "",
    },
    template_item: {
        type: Object,
        default: "",
    },
    isNew: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["save", "cancel"]);

const scriptName = ref(props.name);
const scriptMessage = ref(props.script);
const loading = ref(false);
const checkInvalid = computed(() => {
    if (scriptName.value?.trim() === "")
        return "You must give your call script a name";
    if (scriptMessage.value?.trim() === "")
        return "You must provide a script for the phone call";
    return false;
});

const handleSubmit = async () => {
    if (loading.value) return;
    if (typeof checkInvalid.value === "string")
        return toastError(checkInvalid.value);
    loading.value = true;

    try {
        const res = await axios.post(route("mass-comms.call-templates.store"), {
            script: scriptMessage.value,
            name: scriptName.value,
        });

        if (res.status === "OK" || res.status === 200 || res.status === 201) {
            toastInfo("Call Script Created!");
            emit("save", {
                script: scriptMessage.value,
                id: res?.data?.id,
            });
        }

        loading.value = false;
    } catch (err) {
        console.log("error saving call template", err);
        toastError("There was a problem creating your call script.");
        loading.value = false;
    }
};

onMounted(() => {
    scriptMessage.value = props.script;
});
</script>
