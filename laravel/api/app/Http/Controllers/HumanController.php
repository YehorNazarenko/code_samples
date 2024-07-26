<?php

namespace App\Http\Controllers;

use App\Http\Requests\Humans\AddHumanRequest;
use App\Http\Requests\Humans\UpdateHumanRequest;
use App\Library\Application\Humans\HumanEditor;
use App\Library\Application\Humans\HumanReader;
use App\Library\Fractal\Transformers\Humans\HumanDetailTransformer;
use App\Library\Fractal\Transformers\Humans\HumanListTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class HumanController
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class HumanController extends APIController
{
    /** @var HumanEditor */
    protected $humanEditor;

    /** @var  */
    protected $humanReader;

    //-------------------------------------------------------------------------

    /**
     * HumanController constructor.
     *
     * @param HumanEditor $humanEditor
     * @param HumanReader $humanReader
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct(HumanEditor $humanEditor, HumanReader $humanReader)
    {
        $this->humanEditor = $humanEditor;
        $this->humanReader = $humanReader;
    }

    //-------------------------------------------------------------------------

    /**
     * Handles requests for displaying the list of the human.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = $this->humanReader->listAll($request);

        if ($result) {
            $humans = $result->getCollection();
            $transformedResult = $this->getCustomFractal()->setCollection($humans)
                                    ->setTransformer(new HumanListTransformer())
                                    ->paginatedData($result);

            return $this->responseWithSuccess($transformedResult);
        }

        return $this->responseWithNotFound();
    }






    /**
     * Handles requests for displaying the full list of the humans by place, without pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function fullPlaceList()
    {
        $result = $this->humanReader->fullPlaceList();
        
        if ($result) {
            $transformedResult = $this->getCustomFractal()->setCollection($result)
                                    ->setTransformer(new HumanListTransformer())
                                    ->toArray();

            return $this->responseWithSuccess($transformedResult);
        }

        return $this->responseWithNotFound();
    }

    //-------------------------------------------------------------------------

    /**
     * Handles requests for adding new human details in the database.
     *
     * @param AddHumanRequest $request
     * @return \Psy\Util\Json
     * @throws ValidationException
     */
    public function store(AddHumanRequest $request)
    {
        $result = $this->humanEditor->create($request->all());

        if ($result) {
            return $this->responseWithSuccessfullyCreated($result);
        }

        return $this->responseWithUnAuthorized($result, 'Unauthorized');
        
    }

    //-------------------------------------------------------------------------

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->humanReader->findById($id);

        if (count($result) > 0) {
            $transformedResult = $this->getCustomFractal()->setCollection($result)
                                    ->setTransformer(new HumanDetailTransformer())
                                    ->toArray();

            return $this->responseWithSuccess($transformedResult['data']);
        }

        return $this->responseWithNotFound();
    }


    //-------------------------------------------------------------------------

    /**
     * Handles request for updating the human information.
     *
     * @param UpdateHumanRequest $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws ValidationException
     */
    public function update(UpdateHumanRequest $request, $id)
    {
        $result = $this->humanEditor->update($id, $request->validated());

        if ( $result ) {
            return $this->responseWithSuccess([]);
        }

        return $this->responseWithUnAuthorized([]);
        
    }

    //-------------------------------------------------------------------------

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->humanEditor->delete($id)) {
            return $this->responseWithSuccess([]);
        }

        return $this->responseWithUnAuthorized([]);
    }
}
// end of class
// end of file
