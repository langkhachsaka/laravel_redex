import React, {Component} from 'react';
import ReactPaginate from 'react-paginate';
import _ from 'lodash';

class Paginate extends Component {
    render() {
        return (
            <ReactPaginate
                previousLabel={<span className="ft-chevron-left"/>}
                nextLabel={<span className="ft-chevron-right"/>}
                breakLabel={<a href="">...</a>}
                pageCount={this.props.pageCount}
                marginPagesDisplayed={2}
                pageRangeDisplayed={5}
                onPageChange={this.props.onPageChange}
                containerClassName={"pagination"}
                subContainerClassName={"pages pagination"}
                activeClassName={"active"}
                pageClassName={"page-item"}
                previousClassName={"page-item previous"}
                nextClassName={"page-item next"}
                pageLinkClassName={"page-link"}
                previousLinkClassName={"page-link"}
                nextLinkClassName={"page-link"}
                initialPage={_.get(this.props, 'currentPage', 0)}
                disableInitialCallback={true}
            />
        );
    }
}

export default Paginate;
