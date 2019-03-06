
import * as React from "react";
import * as ReactDOM from "react-dom";
import SortableTree, { changeNodeAtPath, ExtendedNodeData, TreeItem } from 'react-sortable-tree';

interface MenuTreeItem extends TreeItem {
    children?: MenuTreeItem[];
    originalTitle?: string;
    readonly id: number;
    title: any; // Else we cannot set React components
}

type TreeResultResolve = (result: TreeResult) => void;
type ErrorReject = (reason?: any) => void;

interface TreeResult {
    tree: MenuTreeItem[];
}

interface MenuWidgetProps {
    getUrl: string,
    postUrl: string,
    readonly items: MenuTreeItem[];
};

interface MenuWidgetState {
    readonly treeData: TreeItem[];
};

export class MenuWidget extends React.Component<MenuWidgetProps, MenuWidgetState> {

    constructor(props: MenuWidgetProps) {
        super(props);
        this.state = {treeData: this.props.items};
        this.onSaveClick = this.onSaveClick.bind(this);
        this.onResetClick = this.onResetClick.bind(this);
        this.generateNodeProps = this.generateNodeProps.bind(this);
    }

    private onResetClick(event: React.MouseEvent<HTMLButtonElement>) {
        event.preventDefault();
        this.setState({treeData: this.props.items});
    }

    private onSaveClick(event: React.MouseEvent<HTMLButtonElement>) {
        event.preventDefault();
        doTreePost(this.props.postUrl, this.state.treeData)
            .then((result) => this.setState({treeData: result.tree}))
            .catch((err: any) => console.log(err))
        ;
    }

    private generateNodeProps(data: ExtendedNodeData) {
        return {
            title: (<input value={data.node.title as string} onChange={event => {
                // I am not proud of this code, seriously, we reach the limit
                // of react-sortable-tree API comprensionability.
                event.preventDefault();
                const title = event.target.value;
                this.setState({
                    treeData: changeNodeAtPath({
                        treeData: this.state.treeData,
                        path: data.path,
                        getNodeKey: data => data.treeIndex,
                        newNode: { ...data.node, title },
                    })
                });
            }}/>),
        }
    }

    // @todo translations
    render() {
        return (
            <div>
                <SortableTree
                    isVirtualized={false}
                    onChange={treeData => this.setState({ treeData })}
                    treeData={this.state.treeData}
                    generateNodeProps={this.generateNodeProps}
                />
                <div>
                    <button className="button btn btn-primary" onClick={this.onResetClick}>
                        Reset
                    </button>
                    <button className="button btn btn-primary" onClick={this.onSaveClick}>
                        Save
                    </button>
                </div>
            </div>
        );
    }
}

/**
 * Post new version of tree, resolve to the new Tree result.
 */
function doTreePost(url: string, tree: TreeItem[]): Promise<TreeResult> {
    return new Promise<TreeResult>((resolve: TreeResultResolve, reject: ErrorReject) => {
        try {
            const req = new XMLHttpRequest();
            req.open('POST', url);
            req.setRequestHeader("Content-Type", "application/json" );
            req.addEventListener("load", () => {
                if (req.status !== 200) {
                    reject(`${req.status}: ${req.statusText}: ${req.responseText}`);
                } else {
                    try {
                        const result = JSON.parse(req.responseText) as TreeResult;
                        if (!result.tree) {
                            result.tree = [];
                        }
                        resolve(result);
                    } catch (error) {
                        reject(`${req.status}: ${req.statusText}: cannot parse JSON: ${error}`);
                    }
                }
            });
            req.addEventListener("error", () => {
                reject(`${req.status}: ${req.statusText}: ${req.responseText}`);
            });
            req.send(JSON.stringify({tree: tree}));
        } catch (err) {
            reject(err);
        }
    });
}

/**
 * Load tree.
 */
function doTreeGet(url: string): Promise<TreeResult> {
    return new Promise<TreeResult>((resolve: TreeResultResolve, reject: ErrorReject) => {
        const req = new XMLHttpRequest();
        req.open('GET', url);
        req.setRequestHeader("Accept", "application/json" );
        req.addEventListener("load", () => {
            if (req.status !== 200) {
                reject(`${req.status}: ${req.statusText}: ${req.responseText}`);
            } else {
                try {
                    const result = JSON.parse(req.responseText) as TreeResult;
                    if (!result.tree) {
                        result.tree = [];
                    }
                    resolve(result);
                } catch (error) {
                    reject(`${req.status}: ${req.statusText}: cannot parse JSON: ${error}`);
                }
            }
        });
        req.addEventListener("error", () => {
            reject(`${req.status}: ${req.statusText}: ${req.responseText}`);
        });
        req.send();
    });
}

/**
 * Initialize widget.
 */
export function createTreeWidget(element: Element, getUrl: string, postUrl: string) {
    doTreeGet(getUrl)
        .then(result => ReactDOM.render(
            <MenuWidget items={result.tree} getUrl={getUrl} postUrl={postUrl}/>,
            element
         ))
        .catch(err => console.log(err))
    ;
}
