import { ref } from "vue";
// import queries from "@/gql/queries";
// import { useQuery } from '@vue/apollo-composable'

export const queryParam = ref(null);

// export const {data: previewData} = useQuery(queries[query_key.value], query_param);;

// export const setPreviewData = (data) => (previewData.value = data);

export const clearPreviewData = () => {
    queryParam.value = null;
};

export const preview = async (baseRoute, id) => {
    queryParam.value = {
        id: id,
    };
};
