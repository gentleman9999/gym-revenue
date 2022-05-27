<template>
    <TopolEditor
        :options="customOptions"
        @onSave="handleOnSave"
        @onSaveAndClose="handleOnSaveAndClose"
        @init="handleOnInit"
        @loaded="handleOnLoaded"
        @close="handleOnClose"
        v-bind="$attrs"
    />
</template>

<script setup>
import { ref } from "vue";
import { TopolEditor } from "@topol.io/editor-vue";
import { usePage } from "@inertiajs/inertia-vue3";

const emit = defineEmits(["close"]);

const props = defineProps({
    title: {
        type: String,
        default: "Email Template Builder",
    },
    template: {
        type: String,
    },
    productsUrl: {
        type: String,
    },
});

const page = usePage();

const customOptions = {
    title: props.title,
    authorize: {
        // apiKey: "DItrNa1tkR8lXPoTEKq8Mf6MFm2hessWJaCKlNMR0cQ9wmwq0QUuk0GBirAO",
        apiKey: "r00uTVwXAIIXDJMrm3sAZQA6FTbhlKamkvY3KIID40fIrwoa8AXuEKpxY0dx",
        userId: page.props.value.user.id,
    },
    windowBar: ["fullscreen", "close"],
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
    // callbacks: {
    //     // onClose: ()=>emit("close"),
    //     onClose: ()=> {
    //         console.log('onClose');
    //         emit("close")
    //     },
    //     onInit: ()=> {
    //         console.log('INIT!');
    //     },
    // },
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

const handleOnSave = (args) => {
    console.log("handleOnSave", args);
};
const handleOnSaveAndClose = (args) => {
    console.log("handleOnSaveAndClose", args);
};
const handleOnInit = (args) => {
    console.log("handleOnInit", args);
};
const handleOnLoaded = (args) => {
    console.log("handleOnLoaded", args);
};
const handleOnClose = (args) => {
    console.log("handleOnClose", args);
};
</script>
