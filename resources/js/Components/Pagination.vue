<template>
    <div class="btn-group" v-if="paginatorInfo.lastPage > 1">
        <template v-for="(page, ndx) in pages" :key="ndx">
            <div
                class="btn btn-outline hover:text-base-content"
                :class="{
                    'bg-primary': page === paginatorInfo.currentPage,
                    'btn-disabled disabled':
                        (ndx === 0 && paginatorInfo.currentPage === 1) ||
                        (ndx === pages.length - 1 &&
                            paginatorInfo.currentPage ===
                                paginatorInfo.lastPage),
                }"
                @click="updatePage(page, ndx)"
            >
                {{ page }}
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    paginatorInfo: Object,
});

const pages = computed(() => {
    let temp = [props.paginatorInfo.currentPage];
    props.paginatorInfo.currentPage > 1 &&
        temp.unshift(props.paginatorInfo.currentPage - 1);
    props.paginatorInfo.currentPage < props.paginatorInfo.lastPage &&
        temp.push(props.paginatorInfo.currentPage + 1);
    return ["« Previous", ...temp, "Next »"];
});

const emit = defineEmits(["update-page"]);
const updatePage = (page, ndx) => {
    const { currentPage, lastPage } = props.paginatorInfo;
    if (page === currentPage) return;
    if (
        (ndx === 0 && currentPage === 1) ||
        (ndx === pages.value.length - 1 && currentPage === lastPage)
    )
        return;

    let updated = page;
    if (ndx === 0) updated = currentPage - 1;
    else if (ndx === pages.value.length - 1) updated = currentPage + 1;
    emit("update-page", updated);
};
</script>
