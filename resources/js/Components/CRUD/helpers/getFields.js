import { isObject } from "lodash";
import { defaults as defaultTransforms, transforms } from "./transforms";
import {defaults as defaultTypes} from "./types";
import {defaults as defaultComponents, props as includeProps} from "./components";
import {computed} from "vue";

export const getFields = (props) => {
    console.log({props});
    return computed(()=>{
        let fields = props.fields;
        if(!fields){
            //no fields provided, use keys from data
            fields = Object.keys(props.data || props.resource.data[0]);
        }

        fields = fields.map(field=>{
            let _field = field;
            //convert to object if just string provided
            if(!isObject(_field)){
                _field =  {name: _field, label: _field}
            }
            if(!_field.name){
                console.error("object field definitions require a name property", field);
                throw "object field definitions require a name property";
            }
            //set label to name if not set
            if(!_field.label){
                _field.label = _field.name;
            }
            //add data type if missing
            if(!_field.type ){
                if(_field.name in defaultTypes){
                    _field.type = defaultTypes[_field.name];
                }else{
                    _field.type = 'string';
                }
            }
            //add default components if not provided
            if(!_field.component && _field.type in defaultComponents){
                // console.log('found default components for ', _field)
                _field.component = defaultComponents[_field.type];

                //add in extra props
                if( _field.name in includeProps){
                    _field.props = {...includeProps[_field.name], ..._field.props};
                }
            }


            if(!_field.transform){
                // console.log('no transform set for', _field);
                if(_field.name in defaultTransforms){
                    // console.log('found default transform for', _field);
                    _field.transform = defaultTransforms[_field.name];
                }else{
                    // console.log('using noop for', _field);
                    _field.transform = transforms.noop
                    _field.transformNoop = true;
                }
            }else if(typeof _field.transform === 'string'){
                if(_field.transform in transforms){
                    // console.log('found transform for', _field.transform);
                    _field.transform = transforms[_field.transform];
                }else{
                    console.error('Unknown field transform provided', _field);
                    _field.transform = transforms.noop;
                }
            }
            return _field;
        });
        return fields;
    });

};
