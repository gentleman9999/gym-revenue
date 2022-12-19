<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <ApolloQuery :query="(gql) => queries['files']" :variables="form">
        <template v-slot="{ result: { data, loading, error } }">
            <div v-if="loading">Loading...</div>
            <div v-else-if="error">Error</div>
            <div class="files-container" v-else-if="data">
                <div class="row">
                    <file-actions :folderName="data.folderContent.name" />
                    <file-display-mode
                        :display-mode="displayMode"
                        :handleChange="updateDisplayMode"
                    />
                </div>
                <div class="row">
                    <file-nav
                        :folderName="data.folderContent.name"
                        class="file-nav"
                        @rootdir="rootDirectory"
                    />
                    <file-search
                        :form="form"
                        @search="handleFilter('search', $event)"
                        @trashed="handleFilter('trashed', $event)"
                        class="file-search"
                    />
                </div>
                <file-contents
                    v-bind="{ ...data.folderContent }"
                    :displayMode="displayMode"
                    :handleRename="handleRename"
                    :handlePermissions="handlePermissions"
                    :handleTrash="handleTrash"
                    :handleShare="handleShare"
                    :handleRestore="handleRestore"
                    @browse="changeDirectory"
                    @trashed="handleFilter('trashed', 'only')"
                />
            </div>
            <div v-else>Loading...</div>
        </template>
    </ApolloQuery>

    <!-- Section for Modals -->
    <daisy-modal id="confirmModal" ref="confirmModal">
        <confirm-modal :data="item2Remove" @success="confirmTrash" />
    </daisy-modal>
    <daisy-modal
        id="renameModal"
        ref="renameModal"
        @close="selectedItem = null"
    >
        <rename-form
            :item="selectedItem"
            v-if="selectedItem"
            @success="renameModal.close"
        />
    </daisy-modal>

    <daisy-modal
        ref="permissionsModal"
        id="permissionsModal"
        @close="selectedItemPermissions = null"
    >
        <h1 class="font-bold mb-4">Modify Permissions</h1>
        <permissions-form
            :item="selectedItemPermissions"
            v-if="selectedItemPermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
    <daisy-modal ref="shareModal" id="shareModal" @close="folder2Share = null">
        <h1 class="font-bold mb-4">Share with others</h1>
        <share-form
            :item="folder2Share"
            v-if="folder2Share"
            @success="shareModal.close"
        />
    </daisy-modal>
</template>

<style scoped>
.files-container {
    @apply lg:max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 position-unset relative;
}

.row {
    @apply flex flex-row justify-between items-center;
}
.file-nav {
    @apply flex mt-2 md:mt-0;
}
.file-search {
    @apply hidden md:flex;
}
</style>

<script setup>
import { watchEffect, ref, onMounted, watch } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import RenameForm from "./Partials/RenameForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import FileItem from "@/Components/FileItem/index.vue";
import ShareForm from "./Partials/ShareForm.vue";
import Button from "@/Components/Button.vue";
import FileDisplayMode from "./Partials/FileDisplayMode.vue";
import FileActions from "./Partials/FileActions.vue";
import FileContents from "./Partials/FileContents.vue";
import FileSearch from "./Partials/FileSearch.vue";
import FileNav from "./Partials/FileNav.vue";
import ConfirmModal from "./Partials/ConfirmModal.vue";
import mutations from "@/gql/mutations";
import { useMutation } from "@vue/apollo-composable";
import { toastSuccess } from "@/utils/createToast";
import queries from "@/gql/queries";

const props = defineProps({
    sessions: {
        type: Array,
    },

    files: {
        type: Array,
    },
    title: {
        type: String,
    },
    isClientUser: {
        type: Boolean,
    },
    filters: {
        type: Array,
    },
    folderName: {
        type: String,
    },
});

const selectedItem = ref(null);
const selectedItemPermissions = ref(null);
const folder2Share = ref(null);
const item2Remove = ref(null);

const displayMode = ref("desktop");

const renameModal = ref(null);
const permissionsModal = ref(null);
const shareModal = ref(null);
const confirmModal = ref(null);

const handleRename = (data, type) => {
    selectedItem.value = data;
};

const handlePermissions = (data) => {
    selectedItemPermissions.value = data;
};

const handleShare = (data) => {
    folder2Share.value = data;
};

const handleTrash = (data, type) => {
    confirmModal.value.open();
    item2Remove.value = {
        name: type === "file" ? data.filename : data.name,
        id: data.id,
        type: type,
        count: data.files?.length,
    };
};

const { mutate: trashFolder } = useMutation(mutations.folder.trash);
const { mutate: trashFile } = useMutation(mutations.file.trash);
const confirmTrash = async () => {
    let id = item2Remove.value.id;
    if (item2Remove.value.type === "file") {
        let result = await trashFile({ id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(
                `File ${item2Remove.value.name} was successfully removed`
            );
        }
    } else {
        let result = await trashFolder({ id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(
                `Folder ${item2Remove.value.name} was successfully removed`
            );
        }
    }
};

const { mutate: restoreFile } = useMutation(mutations.file.restore);
const { mutate: restoreFolder } = useMutation(mutations.folder.restore);

const handleRestore = async (data, type) => {
    if (type === "file") {
        let result = await restoreFile({ id: data.id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(`File ${result.data.filename} was restored`);
        }
    } else {
        let result = await restoreFolder({ id: data.id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(`Folder ${result.data.name} was restored`);
        }
    }
    confirmModal.value.close();
};

watchEffect(() => {
    if (selectedItem.value) {
        renameModal.value.open();
    }
    if (selectedItemPermissions.value) {
        permissionsModal.value.open();
    }
    if (folder2Share.value) {
        shareModal.value.open();
    }
});

const updateDisplayMode = (value) => {
    displayMode.value = value;
};

const form = ref({
    id: null,
    filter: {
        search: "",
    },
});

const rootDirectory = () => {
    form.value = {
        id: null,
        filter: {
            ...form.value.filter,
            trashed: null,
        },
    };
};

const changeDirectory = (id) => {
    form.value = {
        id: id,
        filter: {
            ...form.value.filter,
            trashed: id ? form.value.filter.trashed : null,
        },
    };
};

const handleFilter = (key, value) => {
    form.value = {
        ...form.value,
        filter: {
            ...form.value.filter,
            [key]: value,
        },
    };
};
</script>
