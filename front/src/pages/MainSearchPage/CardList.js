import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Card, Col, Row, Icon, Tag} from 'antd';
import CardItem from './CardItem'
import 'antd/dist/antd.css';
import './cardlist.css'

class CardList extends Component {



    render() {
        console.log(this.props.cards)
        return (
            <div style={{paddingTop: '30px'}}>
                <Row gutter={16}>
                    {
                        this.props.cards.map((card) =>
                            <CardItem card={card}/>
                        )
                    }
                </Row>
            </div>
        )
    }
}

const mapStateToProps = state => {
    return {
        cards: state.resources.items,
        isFetching: state.resources.isFetching
    }
}

export default connect(mapStateToProps, null)(CardList);



