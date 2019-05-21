import {Menu, Icon, Skeleton, Tree} from 'antd';
import React, {Component} from 'react';
import {connect} from 'react-redux';
import {fetchTree} from '../../actions/index';
import {fetchCategory} from "../../actions";

const SubMenu = Menu.SubMenu;
const MenuItemGroup = Menu.ItemGroup;
const {TreeNode} = Tree;

class CategoryTree extends Component {

    handleClick = e => {
        console.log('click ', e);
    };

    componentDidMount() {
        this.props.onFetchCategory()
    }

    onClick = (e) => {
        console.log(e)
        this.props.onChooseCategory(e[0])
        if(document.getElementsByName('search[search]').length){
            setTimeout(function(){ document.getElementsByName('search[search]')[0].click()}, 1000)
        }
    }


    tree = (data) => {

        if (!data) {
            return;
        }

        return data.map((item) => {

            if (item.__children.length) {
                return (
                    <TreeNode
                        title={item.name}
                        key={item.id}
                        data-id={item.id}
                    >
                        {this.tree(item.__children)}
                    </TreeNode>
                )
            }

            return (
                <TreeNode
                    title={item.name}
                    key={item.id}
                    data-id={item.id}
                >
                </TreeNode>
            )
        });
    };

    render() {
        return (
            <Skeleton loading={this.props.isFetching} active className='skeletonTree'>

                <Tree showLine
                      onSelect={this.onClick}
                >
                    {this.tree(this.props.tree.items)}
                </Tree>

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


export default connect(mapStateToProps, mapDispatchToProps)(CategoryTree)