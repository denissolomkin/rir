import React, {Component} from 'react';
import {connect} from "react-redux";
import {Layout} from 'antd';
import 'antd/dist/antd.css';
import Form from "./Form";
import SideMenu from "./SideMenu";
import CardList from "./CardList";
import MainSearchPageHeader from './MainSearchPageHeader';
import {fetchFormIfNeeded, fetchResources} from "../../actions";


const {Content} = Layout;

class MainSearchPage extends Component {
    render() {
        return (
            <Layout>
                <MainSearchPageHeader/>
                <Layout>

                    <SideMenu/>

                    <Layout style={{padding: '0 24px 24px'}}>
                        <Content
                            style={{
                                background: '#fff',
                                padding: 24,
                                margin: 0,
                                minHeight: 280,
                            }}
                        >

                            <Form onFetchForm={this.props.onFetchForm}
                                  search={this.props.search}
                                  onFetchResources={this.props.onFetchResources}
                                  isFetching={this.props.isFormFetching}
                                  isFetchingResources={this.props.isResourceFetching}
                                  category={this.props.categoryID}
                            />

                            <CardList />

                        </Content>
                    </Layout>
                </Layout>
            </Layout>
        );
    }
}

const mapStateToProps = state => {
    return {
        search: state.search,
        isFormFetching: state.search.isFetching,
        isResourceFetching: state.resources.isFetching,
        categoryID: state.category.id
    }
}

const mapDispatchToProps = dispatch => {
    return {
        onFetchForm: () => {
            dispatch(fetchFormIfNeeded())
        },
        onFetchResources: (callback, query) => {
            dispatch(fetchResources(callback, query))
        }
    }
};

export default connect(mapStateToProps, mapDispatchToProps)(MainSearchPage);
