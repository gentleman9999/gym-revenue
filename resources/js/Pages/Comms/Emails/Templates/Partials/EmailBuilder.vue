<template>
    <DesktopSupportOnly class="lg:hidden" />
    <div class="hidden lg:flex">
        <div
            v-if="showSpinner"
            className="w-full h-full flex items-center justify-center"
        >
            <spinner />
        </div>
        <div
            :class="{ 'sm:w-[60rem] xl:w-[90rem]': true, hidden: showSpinner }"
        >
            <TopolEditor
                v-if="customOptions"
                :options="customOptions"
                v-bind="$attrs"
                @onClose="handleOnClose"
                @onClosed="handleOnClose"
                @onInit="handleOnInit"
                @onLoaded="handleOnLoaded"
                @onBlockSave="createBlock"
                @onBlockRemove="deleteBlock"
                @onBlockEdit="updateBlock"
                @onOpenFileManager="() => (fileManagerVisible = true)"
                :class="{ hidden: showSpinner }"
                style="position: unset"
            />
        </div>

        <daisy-modal ref="blockModal" style="width: 400px">
            <div class="form-control">
                <label for="name" class="label">Name</label>
                <input
                    type="text"
                    v-model="blockForm.name"
                    id="name"
                    class="form-control w-full"
                />
            </div>
            <template #actions>
                <div class="flex-grow" />
                <Button
                    type="button"
                    v-if="blockForm.id !== null"
                    class="btn-success"
                    @click="saveBlock(false)"
                >
                    Update
                </Button>
                <Button
                    type="button"
                    class="btn-secondary"
                    @click="saveBlock(true)"
                >
                    {{ blockForm.id ? "Save As New" : "Save" }}
                </Button>
            </template>
        </daisy-modal>
    </div>
    <template v-if="fileManagerVisible">
        <ModalUnopinionated zindex="9999">
            <div
                class="flex flex-col h-full items-center justify-center bg-black bg-opacity-80"
            >
                <EmailFileManager @image="handleUserFileChoice" />
            </div>
        </ModalUnopinionated>
    </template>
</template>

<script setup>
import { onMounted, onUnmounted, reactive, ref, toRaw, watch } from "vue";
import { topolOptions } from "./topolOptions";
import Button from "@/Components/Button.vue";
import { TopolEditor, TopolPlugin } from "@topol.io/editor-vue";
import { usePage } from "@inertiajs/inertia-vue3";
import Spinner from "@/Components/Spinner.vue";

import DesktopSupportOnly from "./DesktopSupportOnly.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import ModalUnopinionated from "@/Components/ModalUnopinionated.vue";
import EmailFileManager from "@/Components/EmailFileManager.vue";

const emit = defineEmits(["close", "onInit", "onLoaded"]);

const props = defineProps({
    title: {
        type: String,
        default: "Email Template Builder",
    },
    json: {
        type: [Object, String],
        default: {},
    },
    productsUrl: {
        type: String,
    },
    apiKey: {
        type: String,
        required: true,
    },
});

const page = usePage();
const fileManagerVisible = ref(false);

const customOptions = {
    ...topolOptions,
    title: props.title,
    authorize: {
        apiKey: props.apiKey,
        userId: page.props.value.user.id,
    },
    api: {
        ...topolOptions["api"], // default options from config file
        FEEDS: props.productsUrl, // Retrieve feeds of products
    },
};

const ready = ref(false);
const showSpinner = ref(true);
const blocks = ref([]);
const blockModal = ref(null);
const blockForm = reactive({
    name: null,
    id: null,
    definition: null,
    set(id, name) {
        this.id = id;
        this.name = name;
    },
});

// watchers
watch(blocks, (value) => TopolPlugin.setSavedBlocks(toRaw(value)), {
    deep: true,
});

// Methods
function createBlock({ definition }) {
    blockForm.definition = definition;
    blockModal.value.open();
}

function updateBlock(id) {
    console.log("update block:", id);
    const block = blocks.value[getBlockIndexById(id)];
    blockForm.set(id, block.name);
    // I couldn't find a documentation or a better way to do this. Feel free to update
    // If you have a better approach.
    TopolPlugin.load(
        JSON.stringify({
            tagName: "mj-global-style",
            children: [
                {
                    tagName: "mj-container",
                    attributes: {
                        "background-color": "#ffffff",
                        containerWidth: 600,
                    },
                    children: [block.definition],
                },
            ],
            attributes: {
                "mj-text": { "line-height": 1.5 },
                "mj-button": [],
                "mj-section": { "background-color": "#ffffff" },
            },
        })
    );

    TopolPlugin.createNotification({ text: `Editing ${block.name}` });
}

function deleteBlock(id) {
    if (window.confirm("Are you sure?")) {
        axios
            .delete(route("comms.email-templates.delete-block", id))
            .then(({ data }) => {
                blocks.value.splice(getBlockIndexById(id), 1);
            });
    }
}

function saveBlock(isNew = false) {
    console.log("saving block");
    const definition = toRaw(blockForm.definition);
    if (blockForm.id !== null && !isNew) {
        axios
            .put(
                route("comms.email-templates.update-block", blockForm.id),
                blockForm
            )
            .then(({ data }) => {
                const block = blocks.value[getBlockIndexById(blockForm.id)];
                block.definition = definition;
                block.name = blockForm.name;
            });
    } else {
        const blockData = { name: blockForm.name, definition };
        axios
            .post(route("comms.email-templates.create-block"), blockData)
            .then(({ data }) => {
                blocks.value.push({ ...blockData, id: data.id });
            });

        blockForm.set(null, null);
    }

    blockModal.value.close();
    TopolPlugin.createNotification({ text: "Saved", type: "success" });
}

function getBlockIndexById(id) {
    return blocks.value.findIndex((b) => b.id === id);
}

function handleUserFileChoice(url) {
    TopolPlugin.chooseFile(url);
    fileManagerVisible.value = false;
}

const handleOnInit = (args) => {
    ready.value = true;
    showSpinner.value = false;
    emit("onInit", args);

    axios.get(route("comms.email-templates.get-blocks")).then(({ data }) => {
        blocks.value = data.blocks;
    });
};
const handleOnLoaded = (args) => {
    showSpinner.value = false;
    emit("onLoaded", args);
};
const handleOnClose = (args) => {
    console.log("handleOnClose", args);
    emit("onClose", args);
};

axios.get(route("mass-comms.template.tokens")).then(({ data }) => {
    const tokens = [];
    for (const prop in data) {
        tokens.push({ value: data[prop], text: prop });
    }
    customOptions.mergeTags[0].items.unshift(...tokens);
});

watch(
    [ready],
    () => {
        if (ready.value) {
            showSpinner.value = false;
            TopolPlugin.load(props.json);
        }
    },
    { immediate: true }
);

onUnmounted(TopolPlugin.destroy);
</script>
