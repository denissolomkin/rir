import {Menu, Icon, Skeleton} from 'antd';
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {fetchTree} from '../../actions/index';
import {fetchCategory} from "../../actions";

const SubMenu = Menu.SubMenu;
const MenuItemGroup = Menu.ItemGroup;

class CategoryList extends Component {

    handleClick = e => {
        console.log('click ', e);
    };

    componentDidMount() {
        this.props.onFetchCategory()
    }

    onClick = (e) => {
        this.props.onChooseCategory(e.key)
        document.getElementsByName('search[search]')[0].click();
    }

    loop = (data) => {

        if (!data) {
            return;
        }

        return data.map((item) => {

            if (item.__children.length) {
                return (
                    <SubMenu
                        onTitleClick = {this.onClick}
                        data-id={item.id}
                        key={item.id}
                        title={
                            <span>
              <Icon type="folder"/>
              <span>{item.name}</span>
            </span>
                        }
                    >
                        {this.loop(item.__children)}
                    </SubMenu>
                )
            }

            return <Menu.Item
                key={item.id}
                data-id={item.id}
            >
                <Icon type="file"/>
                <span>{item.name}</span>
            </Menu.Item>;

        });
    };

    render() {
        return (
            <Skeleton loading={this.props.isFetching} active className='skeletonTree'>
                <Menu
                    onClick = {this.onClick}
                    mode="inline"
                    defaultSelectedKeys={['1']}
                    defaultOpenKeys={['sub1']}
                    style={{height: '100%', borderRight: 0}}
                >
                    {this.loop(this.props.tree.items)}
                </Menu>
            </Skeleton>

        );
    }
}

const mapStateToProps = (state) => {
    return {
        tree: state.tree
    }
};

const mapDispatchToProps = dispatch => {
    return {
        onFetchCategory: () => {
            dispatch(fetchTree())
        },
        onChooseCategory: (id) => {
            dispatch(fetchCategory(id))
        }
    }
};


export default connect(mapStateToProps, mapDispatchToProps)(CategoryList)