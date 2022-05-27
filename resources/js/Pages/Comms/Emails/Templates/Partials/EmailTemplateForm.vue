<template>
    <ul class="">
        <li><i>Must Use HTML</i></li>
        <li><i>CSS must be inline and raw</i></li>
    </ul>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <div class="form-control">
                    <label for="subject" class="label">Subject Line</label>
                    <input
                        type="text"
                        v-model="form.subject"
                        id="subject"
                        class="form-control w-full"
                    />
                    <jet-input-error
                        :message="form.errors.subject"
                        class="mt-2"
                    />
                </div>
            </div>

            <TopolEditor
                :options="customOptions"
                @onSave="handleOnSave"
            ></TopolEditor>

            <div class="col-span-6">
                <div class="form-control">
                    <label for="template" class="label">Template Markup</label>
                    <textarea
                        v-model="form.markup"
                        id="template"
                        class="form-control w-full"
                        rows="8"
                        cols="40"
                    />
                    <jet-input-error
                        :message="form.errors.markup"
                        class="mt-2"
                    />
                </div>
            </div>
            <div
                class="form-control col-span-6 flex flex-row"
                v-if="canActivate"
            >
                <input
                    type="checkbox"
                    v-model="form.active"
                    autofocus
                    id="active"
                    class="mt-2"
                    :value="true"
                />
                <label for="active" class="label ml-4"
                    >Activate (allows assigning to Campaigns)</label
                >
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>

            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('comms.email-templates'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-accent btn-outline"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
                @click.prevent="modal.open()"
            >
                Preview
            </Button>
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
    <sweet-modal
        title="Preview"
        width="85%"
        overlayTheme="dark"
        modal-theme="dark"
        enable-mobile-fullscreen
        ref="modal"
    >
        <div v-html="form.markup"></div>
    </sweet-modal>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import { TopolEditor } from "@topol.io/editor-vue";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        SweetModal,
        TopolEditor,
    },
    props: ["clientId", "template", "canActivate"],
    setup(props, context) {
        console.log("process.env", process.env);
        const modal = ref(null);
        let template = props.template;
        let operation = "Update";
        if (!template) {
            template = {
                subject: null,
                markup: `<html lang="en">
     <body>

     </body>
</html>`,
                name: null,
                active: false,
                // client_id: props.clientId
            };
            operation = "Create";
        }

        const form = useForm(template);

        let handleSubmit = () =>
            form.put(route("comms.email-templates.update", template.id));
        if (operation === "Create") {
            handleSubmit = () =>
                form.post(route("comms.email-templates.store"), {
                    headers: { "X-Inertia-Modal-Redirect": true },
                });
        }

        const customOptions = {
            authorize: {
                // apiKey: "DItrNa1tkR8lXPoTEKq8Mf6MFm2hessWJaCKlNMR0cQ9wmwq0QUuk0GBirAO",
                apiKey: "r00uTVwXAIIXDJMrm3sAZQA6FTbhlKamkvY3KIID40fIrwoa8AXuEKpxY0dx",
                userId: "philip",
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
                FEEDS: `${process.env.APP_URL}/feeds`,
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
                            value: "*|LAST_NAME|*",
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
                            value: 'For more details, please visit our <a href="https://www.shop.shop">e-shop</a>!',
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

        return {
            form,
            buttonText: operation,
            handleSubmit,
            modal,
            customOptions,
            handleOnSave,
        };
    },
};
</script>
