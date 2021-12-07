<template>
    <tr class="hover">
        <td v-for="(field, index) in fields" class="col-span-3 truncate">
            <component v-if="field.component" :is="field.component" v-bind="{[modelName]: data, data }">
                {{ data[field.label] }}
            </component>
            <template v-else>
                <vue-json-pretty
                    v-if="isObject(data[field.label]) && field.transformNoop"
                    :data="data[field.label]"
                />
                <span
                    v-else
                    :title="field.transform(data[field.label])"
                >
                    {{
                        field.transform(data[field.label])
                    }}
                </span>
            </template>
        </td>
        <td v-if="Object.values(actions).length === 0 || Object.values(actions).filter(action=>action).length">
            <slot name="actions">
                <crud-actions :actions="actions" :data="data" :base-url="baseUrl"/>
            </slot>
        </td>
    </tr>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject } from "lodash";
import CrudActions from "./CrudActions";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import {getFields} from "./getFields";

library.add(faEllipsisH);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard,
        CrudActions
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        actions: {
            type: Object,
            default: {}
        },
        baseUrl:{
            type: String,
        }
    },
    setup(props) {
        const fields = getFields(props);
        console.log({fields:fields.value})

        let __baseUrl = props.baseUrl || props.modelNamePlural || props.modelName+ 's';

        return { fields, isObject, baseUrl: __baseUrl };
    },
};
</script>
