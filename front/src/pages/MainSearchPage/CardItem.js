import React, {Component} from 'react';
import {Card, Col, Icon, Tag} from 'antd';
import CardInfo from './CardInfo'
import 'antd/dist/antd.css';
import './index.css'

class CardItem extends Component {


    state = {
        loading: false,
        visible: false,
    };


    showModal = () => {
        this.setState({
            visible: true,
        });
    };

    handleOk = () => {
        this.setState({loading: true});
        setTimeout(() => {
            this.setState({loading: false, visible: false});
        }, 3000);
    };

    handleCancel = () => {
        this.setState({visible: false});
    };


    render() {
        const {card} = this.props;
        return (
            <Col xs={24} sm={12} md={8} lg={6} xl={6} xxl={4} className='cardWrapper'>
                <Card title={card.title}
                      onClick={this.showModal}
                      className='resourceCard'
                      actions={[<Icon type="user"/>, card.author.username]}>
                    <Tag color="magenta"><Icon type="clock-circle"/> {card.created_at} </Tag>
                    <Tag color="green"><Icon type="file"/> {card.document_type} </Tag>
                    <Tag color="purple"><Icon type="global"/> {card.source} </Tag>
                    <Tag color="blue"><Icon type="file-unknown" theme="twoTone"/> {card.media_type} </Tag>
                </Card>
                <CardInfo card={this.props.card} visible={this.state.visible} loading={this.state.loading} handleOk={this.handleOk}
                          handleCancel={this.handleCancel}/>
            </Col>
        )
    }
}

export default CardItem;



