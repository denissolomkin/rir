import React, {Component} from 'react';
import {Modal, Button} from 'antd';
import {Divider, Col, Row, Icon, Avatar, Tag} from 'antd';
import 'antd/dist/antd.css';

// <p style={pStyle}><Icon type="user"/> {card.author.username}</p>


const pStyle = {
    fontSize: 16,
    color: 'rgba(0,0,0,0.85)',
    lineHeight: '24px',
    display: 'block',
    marginBottom: 16,
};

const DescriptionItem = ({title, content}) => (
    <div
        style={{
            fontSize: 14,
            lineHeight: '22px',
            marginBottom: 7,
            color: 'rgba(0,0,0,0.65)',
        }}
    >
        <p
            style={{
                fontWeight: 'bold',
                marginRight: 8,
                display: 'inline-block',
                color: 'rgba(0,0,0,0.85)',
            }}
        >
            {title}:
        </p>
        {content}
    </div>
);

class CardInfo extends Component {
    render() {
        const {card, visible, loading, handleOk, handleCancel} = this.props;
        return (
            <div>
                <Modal
                    visible={visible}
                    title={card.title}
                    onOk={handleOk}
                    onCancel={handleCancel}
                    footer={[
                        <Button key="back" onClick={handleCancel}>
                            Повернутися
                        </Button>,
                        <Button key="submit" type="primary" loading={loading} onClick={handleOk}>
                            Відкрити
                        </Button>,
                    ]}
                >
                    <div>
                        <p style={pStyle}>{card.author.username}</p>
                        <Row>
                            <Col span={18}>
                                <DescriptionItem title="Тип документа" content={card.document_type}/>{' '}
                            </Col>
                        </Row>
                        <Row>
                            <Col span={12}>
                                <DescriptionItem title="Призначення" content={card.purpose}/>
                            </Col>
                        </Row>
                        <Row>
                            <Col span={24}>
                                <DescriptionItem
                                    title="Ключові слова"
                                    content={card.keywords.join(', ')}
                                />
                            </Col>
                        </Row>
                        <Row>
                            <Col span={12}>
                                <DescriptionItem title="Категорія" content={card.category}/>{' '}
                            </Col>
                            <Col span={12}>
                                <DescriptionItem title="Тип носія" content={card.media_type}/>
                            </Col>
                        </Row>
                        <Row>
                            <Col span={12}>
                                <DescriptionItem title="Джерело" content={card.source}/>
                            </Col>
                            <Col span={12}>
                                <DescriptionItem title="Тема" content={card.theme}/>
                            </Col>
                        </Row>
                        <Row>
                            <Col span={12}>
                                <DescriptionItem title="Формат файлу" content={card.extension}/>
                            </Col>
                            <Col span={12}>
                                <DescriptionItem title="Дата публікації" content={card.published_at.substr(0, 10)}/>
                            </Col>
                        </Row>
                        <Row>
                            <Col span={24}>
                                <DescriptionItem
                                    title="Аннотація"
                                    content={card.annotation}
                                />
                            </Col>
                        </Row>
                    </div>
                </Modal>
            </div>
        )
    }
}

export default CardInfo;



