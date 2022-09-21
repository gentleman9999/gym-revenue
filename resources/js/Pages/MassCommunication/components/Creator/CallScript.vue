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
                Eligendi, delectus!
            </p>

            <!-- text inputs -->
            <div class="flex flex-col">
                <textarea
                    v-model="scriptMessage"
                    cols="30"
                    rows="10"
                    class="mt-4 border-secondary bg-primary-content resize-none"
                ></textarea>
            </div>

            <div class="flex justify-between">
                <button @click="$emit('cancel')">Cancel</button>
                <button
                    @click="
                        $emit('save', {
                            message: scriptMessage,
                        })
                    "
                    class="px-2 py-1 border-secondary border rounded-md mt-4 bg-secondary transition-all text-base-content hover:bg-base-content hover:text-secondary"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    message: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["save", "cancel"]);

const scriptMessage = ref("");

onMounted(() => {
    scriptMessage.value = props.message;
});
</script>
