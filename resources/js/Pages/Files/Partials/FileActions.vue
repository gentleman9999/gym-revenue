<template>
    <div class="flex flex-row space-x-4">
        <Button
            primary
            size="sm"
            @click="Inertia.visitInModal(route('files.upload'))"
        >
            Upload
        </Button>
        <Button primary size="sm" @click="addFolder" v-if="!folderName">
            New Folder
        </Button>
    </div>
</template>
<script setup>
import Button from "@/Components/Button.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
const page = usePage();

const props = defineProps({
    folderName: {
        type: String,
    },
});

const client_id = page.props.value.user.contact_preference.id;
const addFolder = () => {
    Inertia.post(
        route("folders.store"),
        {
            name: "New Folder",
            client_id,
        },
        {
            onSuccess: (response) => {
                console.log("new folder");
                console.log(response);
            },
        }
    );
};
</script>
