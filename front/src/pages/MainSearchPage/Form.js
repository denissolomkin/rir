import 'antd/dist/antd.css';
import './form.css';
import React, {Component} from 'react';
import {Form, Row, Col, Input, Button, Icon, Select, Skeleton} from 'antd';

const {Option} = Select;

class SearchForm extends Component {

    constructor(props) {
        super(props);
    }

    state = {
        expand: false,
        name: ''
    };

    componentDidMount() {
        this.props.onFetchForm()
     }

    getFields = (data) => {
        if (!data) {
            return
        }
        const {getFieldDecorator} = this.props.form;

        return data.map( (item, i) => {
            if(item.id === 'category'){
                console.log(item.name);
            }
            switch (true) {
                case item.id === 'category':
                case item.type === 'hidden':
                    return <Col span={8} key={`${item.name}+${i}`} style={{display: 'none'}}>
                        <Form.Item>
                            {getFieldDecorator(item.name, {
                                initialValue: item.value,
                                rules: [{required: false, message: '', whitespace: true}],
                            })(<Input name={item.name}/>)}
                        </Form.Item>
                    </Col>
                    break;
                case item.type === 'text':
                    return <Col span={8} style={{display: 'block'}} key={`${item.name}+${i}`}>
                        <Form.Item
                            label={<span>{item.label}</span>}
                        >
                            {getFieldDecorator(item.name, {
                                rules: [{required: false, message: '', whitespace: true}],
                            })(<Input name={item.name}/>)}
                        </Form.Item>
                    </Col>
                    break;

                case item.type === 'choice':
                case item.type === 'entity':
                    return <Col span={8} style={{display: 'block'}} key={`${item.name}+${i}`}>
                        <Form.Item label={item.label}>
                            {getFieldDecorator(item.name.replace('[]', ''), {
                                rules: [
                                    {required: false, message: '', type: 'array'},
                                ],
                            })(
                                <Select mode="multiple" placeholder="">
                                    {item.choices.map((item) => <Option key={item.id}
                                                                        value={item.id}>{item.title}</Option>)}
                                </Select>,
                            )}
                        </Form.Item>
                    </Col>
                    break;
                case item.type === 'submit':
                    return <Col span={24} style={{textAlign: 'right'}} key={`${item.name}+${i}`}>
                        <Button type="primary" htmlType="submit" name={item.name}
                                loading={this.props.isFetchingResources}>
                            Пошук
                        </Button>
                        <Button style={{marginLeft: 8}} onClick={this.handleReset}>
                            Очистити
                        </Button>
                        <a style={{marginLeft: 8, fontSize: 12}} onClick={this.toggle}>
                            Collapse <Icon type={this.state.expand ? 'up' : 'down'}/>
                        </a>
                    </Col>
                    break;
            }
        });
    }

    handleSearch = (e) => {
        const {category} = this.props;
        e.preventDefault();

        this.props.form.validateFields((err, values) => {
            if (!err) {

                const buildFormData = (formData, data, parentKey) => {
                    if (data && typeof data === 'object' && !(data instanceof Date) && !(data instanceof File)) {
                        Object.keys(data).forEach(key => {
                            buildFormData(formData, data[key], parentKey ? `${parentKey}[${key}]` : key);
                        });
                    } else {
                        const value = data == null ? '' : data;

                        formData.append(parentKey, value);
                    }
                }

                const jsonToFormData = (data) => {
                    const formData = new FormData();

                    buildFormData(formData, data);

                    return formData;
                }



                values['search[category]'] = category;
                this.props.onFetchResources(jsonToFormData, values);
            }
        });
    };

    handleReset = () => {
        this.props.form.resetFields();
    };

    toggle = () => {
        const {expand} = this.state;
        this.setState({expand: !expand});
    };

    render() {
        return (
            <Skeleton loading={this.props.isFetching} active>
                <Form className="ant-advanced-search-form" onSubmit={this.handleSearch}>
                    <Row gutter={24}>{this.getFields(this.props.search.items)}</Row>
                </Form>
            </Skeleton>
        )
    }
}

export default Form.create({name: 'resources_search'})(SearchForm);



