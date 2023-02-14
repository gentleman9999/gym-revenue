<?php

declare(strict_types=1);

namespace App\Domain\Draftable\Mutations;

use GraphQL\Language\AST\InputValueDefinitionNode;
use GraphQL\Language\AST\NamedTypeNode;
use GraphQL\Language\AST\NameNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Http\Request;
use Laragraph\Utils\RequestParser;
use Nuwave\Lighthouse\Events\EndRequest;
use Nuwave\Lighthouse\Events\StartRequest;
use Nuwave\Lighthouse\GraphQL;
use Nuwave\Lighthouse\Support\Contracts\CreatesContext;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;
use Symfony\Component\HttpFoundation\Response;

class DraftableGraphQLControllerOverride
{
    public function __invoke(
        Request $request,
        GraphQL $graph_QL,
        EventsDispatcher $events_dispatcher,
        RequestParser $request_parser,
        CreatesResponse $creates_response,
        CreatesContext $creates_context
    ): Response {
        $events_dispatcher->dispatch(new StartRequest($request));

        /** @var FieldDefinition $field */
        foreach ($graph_QL->prepSchema()->getConfig()->mutation->astNode->fields as $field) {
            $field->arguments[] = new InputValueDefinitionNode([
                'name'       => new NameNode(['value' => 'is_draft']),
                'type'       => new NamedTypeNode(['name' => new NameNode(['value' => Type::BOOLEAN])]),
                'directives' => new NodeList([]),
            ]);
        }

        $operation_or_operations = $request_parser->parseRequest($request);
        $context                 = $creates_context->generate($request);
        $result                  = $graph_QL->executeOperationOrOperations($operation_or_operations, $context);
        $response                = $creates_response->createResponse($result);

        $events_dispatcher->dispatch(new EndRequest($response));

        return $response;
    }
}
