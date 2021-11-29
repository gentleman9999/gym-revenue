<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>
            <!--            <div class="col-span-6">-->
            <!--                <jet-label for="name" value="Name" />-->
            <!--                <input-->
            <!--                    id="name"-->
            <!--                    type="text"-->
            <!--                    class="block w-full mt-1"-->
            <!--                    v-model="form.name"-->
            <!--                    autofocus-->
            <!--                />-->
            <!--            </div>-->
            <section class="col-span-6 overflow-hidden">
                <div
                    class="max-w-screen-xl mx-auto text-center px-4 sm:px-6 lg:px-8"
                >
                    <div class="max-w-3xl mx-auto lg:max-w-none">
                        <div
                            class="mt-6 sm:mt-5 sm:grid sm:grid-cols-1 sm:gap-4 sm:items-start"
                        >
                            <div class="mt-2 sm:mt-0 sm:col-span-2">
                                <div
                                    v-cloak
                                    @dragover.prevent="
                                        onUploadDragoverEvent($event)
                                    "
                                    @drop.prevent="onUploadDropEvent($event)"
                                    class="w-full flex justify-center items-center px-6 pt-5 pb-6 border-2 border-gray-500 border-dashed rounded-md h-128 transition-colors"
                                    :class="{
                                        'bg-base-100': uploadDragoverTracking,
                                    }"
                                >
                                    <div class="relative z-20 text-center">
                                        <!--                                        <svg-->
                                        <!--                                            class="mx-auto h-24 w-24 text-gray-200"-->
                                        <!--                                            viewBox="0 0 400 460"-->
                                        <!--                                            fill="currentColor"-->
                                        <!--                                        >-->
                                        <!--                                            <path-->
                                        <!--                                                d="M398.286284,360.544249 C393.676808,343.190601 377.828429,331.089442 359.696758,331.078584 L359.696758,331.078584 C341.370574,330.889056 325.342643,343.255751 321.059352,360.889742 L309.821446,360.889742 C298.820948,360.889742 289.871322,351.634464 289.871322,340.752403 L289.871322,280.204163 C293.719701,274.22515 296.899751,267.849313 299.354613,261.190172 L313.986035,261.190172 C356.709227,261.238541 391.382544,227.00309 391.431421,184.724549 C391.462344,158.050472 377.460349,133.284506 354.473815,119.354206 C354.727182,116.428369 354.906733,113.476867 354.906733,110.272661 C354.926683,49.3907725 305.070324,0.0197424893 243.54813,-5.71751646e-06 C221.863342,-0.00690987124 200.648379,6.24948498 182.504738,18.0012017 C167.058354,28.0609871 154.347132,41.7237768 145.491272,57.7872532 C141.97606,57.4160944 138.441895,57.230515 134.906733,57.2315021 C115.356608,57.2028755 96.3471322,63.5856223 80.8518703,75.3827468 C66.8418953,86.0219742 56.1935162,100.398455 50.1586035,116.822232 C43.278803,120.404506 36.9785536,124.983777 31.4653367,130.407039 C1.21995012,160.29618 1.18603491,208.789657 31.3895262,238.720258 C45.919202,253.119442 65.6428928,261.203991 86.2054863,261.190172 L100.836908,261.190172 C103.291771,267.849313 106.47182,274.22515 110.3202,280.204163 L110.3202,340.752403 C110.3202,351.638412 101.370574,360.889742 90.3700748,360.889742 L79.1321696,360.889742 C74.8488778,343.255751 58.8209476,330.889056 40.4947631,331.078584 C18.4438903,331.091416 0.578553616,348.790558 0.591514142,370.611931 C0.603491272,392.433305 18.4897756,410.112704 40.5406484,410.099871 C58.6743142,410.09 74.5236908,397.987854 79.1331671,380.632232 L90.3710723,380.632232 C112.37207,380.632232 130.271322,362.520472 130.271322,340.752403 L130.271322,303.397639 C146.988529,317.973519 167.939152,326.937597 190.121696,329.005622 L190.121696,381.48412 C168.785037,386.936009 155.954115,408.472103 161.463342,429.586695 C166.972569,450.701288 188.735162,463.39867 210.07182,457.946781 C231.408479,452.494893 244.239401,430.958798 238.730175,409.844206 C235.101247,395.936609 224.126683,385.075279 210.07182,381.48412 L210.07182,329.005622 C232.254364,326.938584 253.204988,317.974506 269.922195,303.398627 L269.922195,340.752403 C269.922195,362.524421 287.821446,380.632232 309.822444,380.632232 L321.060349,380.632232 C326.666334,401.735966 348.497756,414.347468 369.823441,408.799828 C391.14813,403.253176 403.892269,381.647983 398.286284,360.544249 Z M40.4947631,390.108627 C29.4763092,390.108627 20.5446384,381.269914 20.5446384,370.366137 C20.5446384,359.462361 29.4763092,350.623648 40.4947631,350.623648 C51.513217,350.623648 60.4448878,359.462361 60.4448878,370.366137 C60.4319202,381.263991 51.5072319,390.095794 40.4947631,390.108627 Z M94.1855362,224.652747 C94.2054863,230.277382 94.6643392,235.892146 95.5571072,241.447702 L86.2054863,241.447702 C54.476808,241.473348 28.7351621,216.041073 28.7092062,184.642618 C28.6962594,169.53073 34.7640898,155.035794 45.5720698,144.360043 C50.4947631,139.547811 56.2314214,135.626953 62.5226933,132.774163 L66.5356608,130.92133 L67.838404,126.733948 C76.9476309,97.4351073 104.527681,76.9739914 134.906733,76.9739914 C139.697756,76.9552361 144.4798,77.3954936 149.185037,78.2888412 L156.885786,79.8129614 L160.189526,72.7619313 C167.461347,57.1890558 178.986534,43.9369099 193.463342,34.4990129 C235.732668,7.14579399 292.405985,18.8797425 320.046883,60.7091416 C329.784539,75.4449356 334.966584,92.6712446 334.958603,110.277597 C334.892768,114.393906 334.613466,118.504292 334.121696,122.591974 L333.309726,130.622232 L339.457357,133.665536 C367.888279,147.588927 379.529177,181.684206 365.459352,209.819227 C355.772569,229.188584 335.824439,241.447702 313.986035,241.447702 L304.634414,241.447702 C305.526185,235.892146 305.983042,230.277382 306.001995,224.652747 C306.001995,166.769742 258.585536,119.846781 200.093766,119.846781 C141.601995,119.846781 94.1855362,166.769742 94.1855362,224.652747 Z M220.045885,419.722361 C220.045885,430.626137 211.114214,439.46485 200.095761,439.46485 C189.077307,439.46485 180.145636,430.626137 180.145636,419.722361 C180.145636,408.818584 189.077307,399.979871 200.095761,399.979871 C211.108229,399.992704 220.032918,408.824506 220.045885,419.722361 Z M200.095761,309.730043 C152.622444,309.730043 114.137656,271.645794 114.137656,224.666567 C114.137656,177.687339 152.622444,139.60309 200.095761,139.60309 C247.569077,139.60309 286.053865,177.687339 286.053865,224.666567 C286,271.624077 247.547132,309.676738 200.095761,309.730043 Z M359.696758,390.108627 C348.678304,390.108627 339.746633,381.269914 339.746633,370.366137 C339.746633,359.462361 348.678304,350.623648 359.696758,350.623648 C370.715212,350.623648 379.646883,359.462361 379.646883,370.366137 C379.633915,381.263991 370.709227,390.095794 359.696758,390.108627 Z M238.288279,208.791631 L206.891771,179.875794 C203.062344,176.348798 197.129177,176.348798 193.299751,179.875794 L161.903242,208.791631 C157.870324,212.50618 157.64389,218.752704 161.396509,222.743648 C165.150125,226.734592 171.462344,226.95867 175.495262,223.245107 L190.120698,209.774807 L190.120698,262.231588 C190.120698,267.683476 194.586534,272.102833 200.095761,272.102833 C205.604988,272.102833 210.070823,267.683476 210.070823,262.231588 L210.070823,209.77382 L224.696259,223.24412 C228.729177,226.95867 235.041397,226.734592 238.795012,222.742661 C242.547631,218.751717 242.321197,212.50618 238.288279,208.791631 Z"-->
                                        <!--                                            />-->
                                        <!--                                        </svg>-->
                                        <upload-icon
                                            :active="uploadDragoverTracking"
                                        />
                                        <p class="mt-4">
                                            Drag and Drop your Files here
                                        </p>
                                        <p class="py-2 text-gray-500">OR</p>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <label class="btn btn-primary"
                                                ><span>Browse Files</span
                                                ><input
                                                    @input="
                                                        form.files = [
                                                            ...form.files,
                                                            ...$event.target
                                                                .files,
                                                        ]
                                                    "
                                                    type="file"
                                                    multiple
                                                    hidden
                                                    class="hidden"
                                            /></label>
                                        </p>
                                        <!--                                        <p class="mt-1 text-xs text-gray-500">-->
                                        <!--                                            .csv, .json, .txt, .shp, .shx, .dbf,-->
                                        <!--                                            .nc and .tiff up to 10GB-->
                                        <!--                                        </p>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul
                        class="shadow overflow-hidden sm:rounded-md mt-5 flex flex-col gap-2"
                    >
                        <file-upload-form
                            v-for="(file, i) in form.files"
                            :file="file"
                            :key="file"
                            :client-id="clientId"
                            @remove="removeFile"
                            @input="fileUploadUpdated"
                            :ref="
                                (el) => {
                                    if (el) fileRefs[i] = el;
                                }
                            "
                        />
                    </ul>
                </div>
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </section>

            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <button
                type="button"
                @click="$inertia.visit(route('files'))"
                :class="{ 'opacity-25': form.processing }"
                class="btn btn-error"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </button>
            <div class="flex-grow" />
            <button
                class="btn btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || formInvalid"
                :loading="form.processing"
            >
                Save
            </button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { ref, computed, onBeforeUpdate, onUnmounted } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import FileUploadForm from "./FileUploadForm";
import UploadIcon from "./UploadIcon";

import Vapor from "laravel-vapor";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    clientId: { type: String, required: true },
});

const uploadDragoverTracking = ref(false);
const uploadDragoverEvent = ref(false);
const uploadProgress = ref(null);

const form = useForm({
    files: [],
});

const onUploadDragoverEvent = (e) => {
    uploadDragoverEvent.value = true;
    uploadDragoverTracking.value = true;
};
const onUploadDropEvent = (e) => {
    uploadDragoverEvent.value = false;
    uploadDragoverTracking.value = false;
    handleDroppedFiles(e);
};
const handleDroppedFiles = (e) => {
    let droppedFiles = e.dataTransfer.files;

    if (!droppedFiles) return;

    [...droppedFiles].forEach((f) => {
        form.files.push(f);
    });
};
const droppedFileValidator = (file) => {
    return false;
};
const removeFile = (file) => {
    form.files = form.files.filter((f, i) => {
        const found = f !== file;
        if (found) {
            fileRefs.value.splice(i, 1);
            fileRefs.value = [...fileRefs.value];
        }
        return found;
    });
};

const fileRefs = ref([]);

onBeforeUpdate(() => {
    console.log("onBeforeUpdate");
    fileRefs.value = [];
});
const uploadedFiles = computed(() =>
    fileRefs.value.filter((ref) => {
        console.log({ form: ref.form, uuid: ref.form.uuid });
        return ref.form.uuid !== null;
    })
);
const numUploadedFiles = computed(() => uploadedFiles.value.length);
const numFiles = computed(() => fileRefs.value.length);
const formInvalid = computed(() => {
    if (numUploadedFiles.value !== numFiles.value) {
        return true;
    }
    return form.files.length === 0;
});

// const handleSubmit = () => form.post(`/files`);

const allFiles = computed(() => fileRefs.value.map((fileRef) => fileRef.form.data()));

const handleSubmit = () => {
    Inertia.post(route("files.store"), allFiles.value);
};

const removeRouteGaurd = Inertia.on("before", ({ detail: { visit } }) => {
    const { method } = visit;
    if (method === "get" && numUploadedFiles.value) {
        return confirm(
            "You haven't yet saved your uploaded files.  Are you sure you want to navigate away?"
        );
    }
});

onUnmounted(removeRouteGaurd);
</script>
