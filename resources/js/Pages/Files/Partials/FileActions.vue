<template>
    <div class="flex flex-row space-x-4">
        <Button primary size="sm" @click="handleFileUpload"> Upload </Button>
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
    refetch: {
        type: Function,
    },
    uploadModal: {
        type: Object,
        required: true,
    },
});

const { mutate: createFolder } = useMutation(mutations.folder.create);
const addFolder = async () => {
    let result = await createFolder({
        name: "New Folder",
    });
    if (result && result.data) {
        props.refetch();
        toastSuccess("Folder Created");
    }
};

const handleFileUpload = () => {
    console.log({ uploadModal: props.uploadModal });
    props.uploadModal.open();

    // Inertia.visitInModal(route('files.upload'))
};
</script>
