<template>
    <div
        v-if="showSpinner"
        class="w-full h-full flex items-center justify-center"
    >
        <spinner />
    </div>
    <TopolEditor
        :options="customOptions"
        v-bind="$attrs"
        @onClose="handleOnClose"
        @onClosed="handleOnClose"
        @onInit="handleOnInit"
        @onLoaded="handleOnLoaded"
        :class="{ hidden: showSpinner }"
        style="position: unset"
    />
</template>

<script setup>
import { ref, watch, defineEmits } from "vue";
import { TopolEditor, TopolPlugin } from "@topol.io/editor-vue";
import { usePage } from "@inertiajs/inertia-vue3";
import Spinner from "@/Components/Spinner.vue";

const emit = defineEmits(["close", "onInit", "onLoaded"]);

const props = defineProps({
    title: {
        type: String,
        default: "Email Template Builder",
    },
    json: {
        type: Object,
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

const customOptions = {
    title: props.title,
    authorize: {
        apiKey: props.apiKey,
        userId: page.props.value.user.id,
    },
    disableAlerts: true,
    windowBar: ["fullscreen", "close"],
    topBarOptions: [
        "undoRedo",
        "changePreview",
        "previewSize",
        // "previewTestMail",
        "save",
        // "saveAndClose",
    ],
    // callbacks: {
    //     // onClose: ()=>emit("close"),
    //     onClose: () => {
    //         console.log("onClose");
    //         emit("close");
    //     },
    // },
    //this could be cool to add in a clients plans. we just need to hide it until we know how to
    //tap into the API to pull in the plans
    contentBlocks: {
        // product: {
        //     hidden: true,
        // },
    },
    // savedBlocks: [],
    api: {
        // Your own endpoint for uploading images
        IMAGE_UPLOAD: "/images/upload",
        // Your own endpoint for getting contents of folders
        FOLDERS: "/images/folder-contents",
        // Your own endpoint to retrieve base64 image edited by Image Editor
        IMAGE_EDITOR_UPLOAD: "/images/image-editor-upload",
        // Create Autosave
        // AUTOSAVE: "/autosave",
        // Retreive all autosaves
        // AUTOSAVES: "/autosaves",
        // Retreive an autosave
        // GET_AUTOSAVE: "/autosave/",
        // Retrieve feeds of products
        FEEDS: props.productsUrl,
        // Retrieve products from feed
        PRODUCTS: "/products",
    },
    mergeTags: [
        {
            name: "Merge tags", // Group name
            items: [
                {
                    value: "%%recipient.first_name%%", // Text to be inserted
                    text: "First name", // Shown text in the menu
                    label: "Customer's first name", // Shown description title in the menu
                },
                {
                    value: "%%recipient.first_name%%",
                    text: "Last name",
                    label: "Customer's last name",
                },

                //Nested Merge Tags
                {
                    name: "Some Group",
                    items: [
                        {
                            value: "*|FIRST_NAME_NESTED|*",
                            text: "First name 2",
                            label: "Customer's first name 2",
                        },
                        {
                            value: "*|LAST_NAME_NESTED|*",
                            text: "Last name 2",
                            label: "Customer's last name 2",
                        },
                    ],
                },
            ],
        },
        {
            name: "Special links", // Group name
            items: [
                {
                    value: '<a href="*|UNSUBSCRIBE_LINK|*">Unsubscribe</a>',
                    text: "Unsubscribe",
                    label: "Unsubscribe link",
                },
                {
                    value: '<a href="*|WEB_VERSION_LINK|*">Web version</a>',
                    text: "Web version",
                    label: "Web version link",
                },
            ],
        },
        {
            name: "Special content", // Group name
            items: [
                {
                    value: 'For more details, please visit our <a href="https://www.shop.shop">website</a>!',
                    text: "Visit our site",
                    label: "Call to Action",
                },
            ],
        },
    ],
};
const ready = ref(false);
const showSpinner = ref(true);
const handleOnSave = (args) => {
    console.log("handleOnSave", args);
};
const handleOnSaveAndClose = (args) => {
    console.log("handleOnSaveAndClose", args);
};
const handleOnInit = (args) => {
    ready.value = true;
    if (!props.json) {
        showSpinner.value = false;
    }
    emit("onInit", args);
};
const handleOnLoaded = (args) => {
    showSpinner.value = false;
    emit("onLoaded", args);
};
const handleOnClose = (args) => {
    console.log("handleOnClose", args);
    emit("onClose", args);
};
// onMounted(()=>{
//
// })
watch(
    [ready],
    () => {
        if (ready.value && props.json) {
            console.log("trying to load topol json", props.json);
            TopolPlugin.load(JSON.stringify(props.json));
        }
    },
    { immediate: true }
);
</script>
