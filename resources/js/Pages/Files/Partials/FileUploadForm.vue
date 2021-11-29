<template>
    <li>
        <form @submit="handleSubmit">
            <div
                class="block focus:outline-none transition duration-150 ease-in-out relative bg-base-200 hover:bg-base-100 rounded-xl"
            >
                <progress
                    v-if="isUploading"
                    :value="uploadProgress"
                    max="100"
                    class="absolute inset-0 w-full h-full opacity-50"
                >
                    {{ uploadProgress }}%
                </progress>
                <div class="px-4 py-4 sm:px-6 relative z-1">
                    <div class="flex items-center justify-between">
                        <div
                            class="flex w-full items-center text-sm leading-5 font-medium text-secondary p-2 gap-4"
                        >
                            <div class="relative">
                                <font-awesome-icon icon="file" size="3x" />
                                <div
                                    class="absolute inset-0 text-base-content flex items-center justify-center text-xs font-bold uppercase"
                                >
                                    {{ file.type.split("/")[1] }}
                                </div>
                                <span
                                    class="absolute bottom-0 inset-x-0 text-gray-400 text-2xs transform translate-y-full whitespace-nowrap flex justify-center"
                                >
                                    {{ prettyBytes(file.size) }}
                                </span>
                            </div>

                            <input
                                type="text"
                                class="input-ghost input-sm w-full"
                                :value="form.filename"
                                @input="form.filename = $event.target.value"
                            />
                        </div>
                        <div class="hidden md:flex items-center gap-2">
                            <div
                                v-if="uploadProgress"
                                class="badge"
                                :class="{
                                    'badge-success': uploadProgress === 100,
                                    'badge-warning': uploadProgress < 100,
                                }"
                            >
                                {{
                                    uploadProgress < 100
                                        ? `${uploadProgress}%`
                                        : "Uploaded"
                                }}
                            </div>
                            <button
                                class="btn btn-ghost btn-error btn-circle"
                                @click.prevent="emit('remove', file)"
                            >
                                <font-awesome-icon icon="times" size="lg" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </li>
</template>

<style scoped>
progress[value]::-webkit-progress-bar {
    @apply bg-transparent;
}
progress::-webkit-progress-value {
    @apply bg-primary;
}
</style>
<script setup>
import { ref, computed, onMounted, watchEffect } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import Vapor from "laravel-vapor";
import { Inertia } from "@inertiajs/inertia";
import prettyBytes from "pretty-bytes";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimes, faFile } from "@fortawesome/pro-solid-svg-icons";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
library.add(faTimes, faFile);

const props = defineProps({
    clientId: { type: String, required: true },
    file: { type: Object, required: true },
});

const emit = defineEmits(["remove"]);

const uploadProgress = ref(null);

const form = useForm({
    id: null,
    key: null,
    extension: null,
    bucket: null,
    filename: props.file.name,
    original_filename: props.file.name,
    client_id: props.clientId,
    size: props.file.size,
    // is_public: true,
});

const removeFile = (file) => {
    console.log({ files: form.files, file });
    form.files = form.files.filter((f) => {
        return f !== file;
    });
};

// const handleSubmit = () => form.post(`/files`);
const handleSubmit = async () => {
    let response = await Vapor.store(props.file, {
        // visibility: form.isPublic ? 'public-read' : null,
        visibility: 'public-read',
        progress: (progress) => {
            uploadProgress.value = Math.round(progress * 100);
        },
    });
    form.id = response.uuid;
    form.key = response.key;
    form.extension = response.extension;
    form.bucket = response.bucket;
};

const isUploading = computed(() => {
    return uploadProgress.value !== null && uploadProgress.value < 100;
});

onMounted(handleSubmit);

defineExpose({ form });
</script>
