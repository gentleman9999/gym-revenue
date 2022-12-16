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
import { usePage } from "@inertiajs/inertia-vue3";
import mutations from "@/gql/mutations";
import { useMutation } from "@vue/apollo-composable";
import { toastSuccess } from "@/utils/createToast";

const page = usePage();
const props = defineProps({
    folderName: {
        type: String,
    },
});

const { mutate: createFolder } = useMutation(mutations.folder.create);
const addFolder = async () => {
    let result = await createFolder({
        name: "New Folder",
    });
    if (result && result.data) {
        toastSuccess("Folder Created");
    }
};
</script>
