<template>
    <LayoutHeader title="File Management">
        <h2 class="font-semibold text-xl leading-tight">File Manager</h2>
    </LayoutHeader>
    <div class="files-container" v-if="data">
        <div class="row">
            <file-actions
                :folderName="data?.folder?.name || 'Home'"
                :refetch="refetch"
                :upload-modal="uploadModal"
            />
            <file-display-mode
                :display-mode="displayMode"
                :handleChange="updateDisplayMode"
            />
        </div>
        <div class="row">
            <file-nav
                :folderName="data?.folder?.name || 'Home'"
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
            :folders="{ ...(data?.folder?.id ? [] : data?.folders) }"
            :files="{ ...(data?.folder?.files || data?.files) }"
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
            @success="confirmRename"
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
    <daisy-modal ref="uploadModal" id="uploadModal">
        <Upload />
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
import { computed, ref, watchEffect } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import RenameForm from "./Partials/RenameForm.vue";
import PermissionsForm from "./Partials/PermissionsForm.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import ShareForm from "./Partials/ShareForm.vue";
import FileDisplayMode from "./Partials/FileDisplayMode.vue";
import FileActions from "./Partials/FileActions.vue";
import FileContents from "./Partials/FileContents.vue";
import FileSearch from "./Partials/FileSearch.vue";
import FileNav from "./Partials/FileNav.vue";
import ConfirmModal from "./Partials/ConfirmModal.vue";
import mutations from "@/gql/mutations";
import { useMutation, useQuery } from "@vue/apollo-composable";
import { toastSuccess } from "@/utils/createToast";
import queries from "@/gql/queries";
import Upload from "./Partials/Upload.vue";

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
const uploadModal = ref(null);

const form = ref({
    id: null,
    filter: {
        search: "",
    },
});

const { result, refetch } = useQuery(queries["files"], form, {
    throttle: 500,
});

const data = computed(() => {
    if (result.value) {
        return _.cloneDeep(result.value);
    } else {
        return null;
    }
});

const handleRename = (data, type) => {
    selectedItem.value = data;
};

const confirmRename = (data, type) => {
    renameModal.value.close();
    refetch();
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
            refetch();
        }
    } else {
        let result = await trashFolder({ id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(
                `Folder ${item2Remove.value.name} was successfully removed`
            );
            refetch();
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
            refetch();
        }
    } else {
        let result = await restoreFolder({ id: data.id });
        if (result.data) {
            confirmModal.value.close();
            toastSuccess(`Folder ${result.data.name} was restored`);
            refetch();
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
