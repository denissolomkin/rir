import React, {Component} from 'react';
import {connect} from 'react-redux';
import {Layout} from 'antd';
import 'antd/dist/antd.css';
import './index.css'
import {fetchTree} from "../../actions";
import CategoryTree from './CategoryTree';

const {Sider} = Layout;

class SideMenu extends Component {

    componentDidMount() {
        this.props.onFetchTree()
    }

    render() {
        return (
            <Sider width={270} style={{background: '#fff'}}>
                <div className="logo"/>
                    <CategoryTree isFetching={this.props.isCategoryFetching}/>
            </Sider>
        )
    }
}

const mapStateToProps = state => {
    return {
        isCategoryFetching: state.tree.isFetching
    }
}

const mapDispatchToProps = dispatch => {
    return {
        onFetchTree: () => {
            dispatch(fetchTree())
        }
    }
};

export default connect(mapStateToProps, mapDispatchToProps)(SideMenu);



