<template>
    <div class="bg-neutral border border-secondary rounded-xl p-8">
        <h2 class="text-2xl mb-6">Email Template Image Manager</h2>
        <div
            v-if="currentlyUploadingFiles.length === 0"
            class="py-4 flex justify-center"
        >
            <button
                @click="inputRef.click()"
                class="selector-btn bg-primary hover:bg-secondary active:opacity-50 disabled:opacity-25 disabled:cursor-not-allowed"
            >
                Upload File
            </button>
        </div>
        <input
            ref="inputRef"
            hidden
            @change="handleFileChange"
            multiple
            type="file"
            accept="image/*"
        />

        <!-- files currently being uploaded -->
        <ul class="flex gap-6 p-4 max-w-[32rem] overflow-x-auto">
            <li
                v-for="file in currentlyUploadingFiles"
                :key="file.id"
                class="max-w-[10rem] min-w-[10rem] flex flex-col justify-between bg-black bg-opacity-10 rounded-lg p-2"
            >
                <div class="my-2">
                    <p class="overflow-hidden text-ellipsis whitespace-nowrap">
                        {{ file.name }}
                    </p>
                    <p>{{ formatBytes(file.size) }}</p>
                </div>

                <div class="max-w-[5rem] mx-auto">
                    <img :src="file.data" />
                </div>
                <!-- progress bar (tbd) -->
                <template v-if="file.status !== 'done'">
                    <div class="flex items-center gap-2">
                        <progress
                            :value="file.progress"
                            max="100"
                            :id="`${file.localId}-upload`"
                        ></progress>
                        <label :for="`${file.localId}-upload`"
                            >{{ file.progress }}%</label
                        >
                    </div>
                    <!-- control buttons -->
                    <div>
                        <button @click="file.abort()">Cancel</button>
                    </div>
                </template>

                <template v-if="file.status === 'done'">
                    <button>Uploaded</button>
                </template>
            </li>
        </ul>

        <div class="my-4">
            <input
                v-model="searchValue"
                type="text"
                placeholder="search"
                class="w-full"
            />
        </div>

        <!-- file list -->
        <template v-if="!fileList">
            <div class="flex justify-center h-[24rem]">
                <Spinner />
            </div>
        </template>

        <template v-else>
            <ul class="h-[24rem] overflow-y-auto mt-8 bg-black bg-opacity-10">
                <EmailImageItem
                    v-for="file in filteredFiles"
                    :key="file.id"
                    :file="file"
                    @selected="(imageUrl) => $emit('image', imageUrl)"
                />
            </ul>
        </template>
    </div>
</template>

<style scoped>
.selector-btn {
    @apply px-2 py-1 block transition-all rounded-md border border-transparent;
}
</style>

<script setup>
import axios from "axios";
import { ref, onMounted, computed } from "vue";
import {
    genId,
    formatBytes,
} from "@/Pages/MassCommunication/components/Creator/helpers";
import Vapor from "laravel-vapor";
import EmailImageItem from "./EmailImageItem.vue";
import Spinner from "@/Components/Spinner.vue";

const emit = defineEmits(["image"]);
const inputRef = ref();
const fileList = ref(null);
const currentlyUploadingFiles = ref([]);
const searchValue = ref("");
const filteredFiles = computed(() => {
    const rexp = new RegExp(searchValue.value, "gi");
    if (!fileList.value) return [];
    return [...fileList.value.map((f) => rexp.test(f.filename) && f)].filter(
        (fi) => fi
    );
});

/**
 * Fetches users email template images
 * fileList is also indicative of loading status while it's value is falsy
 */
async function loadFiles() {
    searchValue.value = "";
    currentlyUploadingFiles.value = [];
    fileList.value = null;

    await axios
        .get("email-templates/images")
        .then(({ data }) => {
            console.log("data", data);
            fileList.value = data;
        })
        .catch((err) => {
            fileList.value = [];
            console.log("couldnt retreive file list", err);
        });
}

/***** Upload & State helper fns *****/

/**
 * File handler
 * batches file uploads into abortable, progress updated interfaces we can visualize via a display driver
 */
async function handleFileChange(event) {
    let files = event.target.files;
    if (files.length === 0) return;

    console.clear();
    const res = await Promise.all(
        [...files].map((fileData) => {
            return new Promise((resolve, reject) => {
                const controller = new AbortController();

                let localFile = {
                    localId: genId(),
                    size: fileData.size,
                    name: fileData.name,
                    status: "init",
                    progress: 0,
                    data: URL.createObjectURL(fileData),
                    abort: controller.abort,
                };

                currentlyUploadingFiles.value = [
                    ...currentlyUploadingFiles.value,
                    localFile,
                ];

                Vapor.store(fileData, {
                    visibility: "public-read",
                    progress: (progress) =>
                        updateUploadProgress(progress, localFile.localId),
                }).then((response) => {
                    axios
                        .post(route("mass-comms.email-templates.store-files"), {
                            ...response,
                            size: fileData.size,
                            id: response.uuid,
                            is_public: "1",
                            original_filename: fileData.name,
                            filename: fileData.name,
                        })
                        .then((data) => resolve(data));
                });
            });
        })
    );

    console.log("ALL files", res);
    await loadFiles();
}

/**
 * Handles the upload progress of a file item
 * @param {number} progress
 * @param {string} localId
 */
function updateUploadProgress(progress, localId) {
    let current = Math.round(progress * 100);
    let status = current >= 100 ? "done" : "uploading";

    updateLocalFile(localId, { progress: current, status: status });
}

/**
 * Update local file state
 * @param {string} localId
 * @param {object} data modified fields
 */
function updateLocalFile(localId, data) {
    currentlyUploadingFiles.value = [...currentlyUploadingFiles.value].map(
        (file) => {
            if (file.localId === localId) {
                for (const field of Object.keys(data)) {
                    file[field] = data[field];
                }
            }
            return file;
        }
    );
}

onMounted(loadFiles);
</script>
